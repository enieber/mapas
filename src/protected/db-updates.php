<?php
namespace MapasCulturais;

$app = App::i();
$em = $app->em;
$conn = $em->getConnection();

return [


    'new random id generator' => function () use ($conn) {
        $conn->executeQuery("
            CREATE SEQUENCE pseudo_random_id_seq
                START WITH 1
                INCREMENT BY 1
                NO MINVALUE
                NO MAXVALUE
                CACHE 1;");

        $conn->executeQuery("
            CREATE OR REPLACE FUNCTION pseudo_random_id_generator() returns int AS $$
                DECLARE
                    l1 int;
                    l2 int;
                    r1 int;
                    r2 int;
                    VALUE int;
                    i int:=0;
                BEGIN
                    VALUE:= nextval('pseudo_random_id_seq');
                    l1:= (VALUE >> 16) & 65535;
                    r1:= VALUE & 65535;
                    WHILE i < 3 LOOP
                        l2 := r1;
                        r2 := l1 # ((((1366 * r1 + 150889) % 714025) / 714025.0) * 32767)::int;
                        l1 := l2;
                        r1 := r2;
                        i := i + 1;
                    END LOOP;
                    RETURN ((r1 << 16) + l1);
                END;
            $$ LANGUAGE plpgsql strict immutable;");
    },

    'migrate gender' => function() use ($conn) {
        $conn->executeQuery("UPDATE agent_meta SET value='Homem' WHERE key='genero' AND value='Masculino'");
        $conn->executeQuery("UPDATE agent_meta SET value='Mulher' WHERE key='genero' AND value='Feminino'");
    },


    'remove circular references again... ;)' => function() use ($conn) {
        $conn->executeQuery("UPDATE agent SET parent_id = null WHERE id = parent_id");

        $conn->executeQuery("UPDATE agent SET parent_id = null WHERE id IN (SELECT profile_id FROM usr)");

        return false; // executa todas as vezes só para garantir...
    },

    'create table user apps' => function() use ($conn) {

        $conn->executeQuery("CREATE TABLE user_app (
                                public_key character varying(64) NOT NULL,
                                private_key character varying(128) NOT NULL,
                                user_id integer NOT NULL,
                                name text NOT NULL,
                                status integer NOT NULL,
                                create_timestamp timestamp NOT NULL
                                );");

        $conn->executeQuery("ALTER TABLE ONLY user_app ADD CONSTRAINT user_app_pk PRIMARY KEY (public_key);");

        $conn->executeQuery("ALTER TABLE ONLY user_app ADD CONSTRAINT usr_user_app_fk FOREIGN KEY (user_id) REFERENCES usr(id);");

    },


    'create table user_meta' => function() use ($conn) {

        if($conn->fetchAll("SELECT table_name FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'user_meta';")){
            echo "TABLE user_meta ALREADY EXISTS";
            return true;
        }

        $conn->executeQuery("CREATE TABLE user_meta (
                                object_id integer NOT NULL,
                                key character varying(32) NOT NULL,
                                value text,
                                id integer NOT NULL);");

        $conn->executeQuery("CREATE SEQUENCE user_meta_id_seq
                                START WITH 1
                                INCREMENT BY 1
                                NO MINVALUE
                                NO MAXVALUE
                                CACHE 1;");

        $conn->executeQuery("ALTER SEQUENCE user_meta_id_seq OWNED BY user_meta.id;");
        $conn->executeQuery("ALTER TABLE ONLY user_meta ALTER COLUMN id SET DEFAULT nextval('user_meta_id_seq'::regclass);");
        $conn->executeQuery("ALTER TABLE ONLY user_meta ADD CONSTRAINT user_meta_pk PRIMARY KEY (id);");
        $conn->executeQuery("CREATE INDEX user_meta_owner_key_index ON user_meta USING btree (object_id, key);");
        $conn->executeQuery("CREATE INDEX user_meta_owner_key_value_index ON user_meta USING btree (object_id, key, value);");
        $conn->executeQuery("ALTER TABLE ONLY user_meta ADD CONSTRAINT usr_user_meta_fk FOREIGN KEY (object_id) REFERENCES usr(id);");
    },

    'create seal and seal relation tables' => function() use ($conn) {

        if($conn->fetchAll("SELECT table_name FROM information_schema.tables WHERE  table_schema = 'public' AND table_name = 'seal';")){
            echo "TABLE seal ALREADY EXISTS";
            return true;
        }

        $conn->executeQuery("CREATE TABLE seal (id INT NOT NULL, agent_id INT NOT NULL, name VARCHAR(255) NOT NULL, short_description TEXT DEFAULT NULL, long_description TEXT DEFAULT NULL, valid_period SMALLINT NOT NULL, create_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT NOT NULL, certificate_text TEXT DEFAULT NULL, PRIMARY KEY(id));");
        $conn->executeQuery("CREATE SEQUENCE seal_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $conn->executeQuery("CREATE TABLE seal_relation (id INT NOT NULL, seal_id INT DEFAULT NULL, object_id INT NOT NULL, create_timestamp TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status SMALLINT DEFAULT NULL, object_type VARCHAR(255) NOT NULL, agent_id INTEGER NOT NULL, PRIMARY KEY(id));");
        $conn->executeQuery("CREATE SEQUENCE seal_relation_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $conn->executeQuery("CREATE TABLE seal_meta (id INT NOT NULL, object_id INT DEFAULT NULL, key VARCHAR(255) NOT NULL, value TEXT DEFAULT NULL, PRIMARY KEY(id));");
        $conn->executeQuery("CREATE SEQUENCE seal_meta_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $conn->executeQuery("ALTER TABLE seal ADD CONSTRAINT seal_fk FOREIGN KEY (agent_id) REFERENCES agent (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("ALTER TABLE seal_meta ADD CONSTRAINT seal_meta_fk FOREIGN KEY (object_id) REFERENCES seal (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("ALTER TABLE ONLY seal_relation ADD CONSTRAINT seal_relation_fk FOREIGN KEY (seal_id) REFERENCES seal(id);");

    },

    'resize entity meta key columns' => function() use($conn) {
        $conn->executeQuery('ALTER TABLE space_meta ALTER COLUMN key TYPE varchar(128)');
        $conn->executeQuery('ALTER TABLE agent_meta ALTER COLUMN key TYPE varchar(128)');
        $conn->executeQuery('ALTER TABLE event_meta ALTER COLUMN key TYPE varchar(128)');
        $conn->executeQuery('ALTER TABLE project_meta ALTER COLUMN key TYPE varchar(128)');
        $conn->executeQuery('ALTER TABLE user_meta ALTER COLUMN key TYPE varchar(128)');
    },


    'create registration field configuration table' => function () use($conn){
        $conn->executeQuery("CREATE TABLE registration_field_configuration (id INT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, categories TEXT DEFAULT NULL, required BOOLEAN NOT NULL, field_type VARCHAR(255) NOT NULL, field_options VARCHAR(255) NOT NULL, PRIMARY KEY(id));");
        $conn->executeQuery("CREATE INDEX IDX_60C85CB1166D1F9C ON registration_field_configuration (project_id);");
        $conn->executeQuery("COMMENT ON COLUMN registration_field_configuration.categories IS '(DC2Type:array)';");
        $conn->executeQuery("CREATE SEQUENCE registration_field_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
        $conn->executeQuery("ALTER TABLE registration_field_configuration ADD CONSTRAINT FK_60C85CB1166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
    },

    'alter table registration_file_configuration add categories' => function () use($conn){
        $conn->executeQuery("ALTER TABLE registration_file_configuration DROP CONSTRAINT registration_meta_project_fk;");
        $conn->executeQuery("ALTER TABLE registration_file_configuration ADD categories TEXT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE registration_file_configuration ALTER id DROP DEFAULT;");
        $conn->executeQuery("ALTER TABLE registration_file_configuration ALTER project_id DROP NOT NULL;");
        $conn->executeQuery("ALTER TABLE registration_file_configuration ALTER required DROP DEFAULT;");
        $conn->executeQuery("COMMENT ON COLUMN registration_file_configuration.categories IS '(DC2Type:array)';");
        $conn->executeQuery("ALTER TABLE registration_file_configuration ADD CONSTRAINT FK_209C792E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
    },

    'create saas tables' => function () use($conn) {
      $conn->executeQuery("CREATE TABLE saas (id INT NOT NULL, name VARCHAR(255) NOT NULL, create_timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status SMALLINT NOT NULL, agent_id INTEGER NOT NULL, PRIMARY KEY(id), url VARCHAR(255) NOT NULL, url_parent VARCHAR(255), slug VARCHAR(50) NOT NULL, namespace VARCHAR(50) NOT NULL);");
      $conn->executeQuery("CREATE SEQUENCE saas_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
      $conn->executeQuery("CREATE TABLE saas_meta ( object_id integer NOT NULL, key character varying(128) NOT NULL, value text, id integer NOT NULL);");
      $conn->executeQuery("CREATE SEQUENCE saas_meta_id_seq INCREMENT BY 1 MINVALUE 1 START 1;");
      $conn->executeQuery("ALTER TABLE ONLY saas_meta ADD CONSTRAINT saas_saas_meta_fk FOREIGN KEY (object_id) REFERENCES saas(id);");
    },
            
    'rename saas tables to subsite' => function () use($conn) {
        $conn->executeQuery("ALTER TABLE saas RENAME TO subsite");
        $conn->executeQuery("ALTER TABLE saas_meta RENAME TO subsite_meta");
        $conn->executeQuery("ALTER SEQUENCE saas_id_seq RENAME TO subsite_id_seq");
        $conn->executeQuery("ALTER SEQUENCE saas_meta_id_seq RENAME TO subsite_meta_id_seq");
    },
            
    'remove parent_url and add alias_url' => function () use($conn) {
        $conn->executeQuery("ALTER TABLE subsite DROP COLUMN url_parent");
        $conn->executeQuery("ALTER TABLE subsite ADD COLUMN alias_url VARCHAR(255) DEFAULT NULL;");
        
        $conn->executeQuery("CREATE INDEX url_index ON subsite (url);");
        $conn->executeQuery("CREATE INDEX alias_url_index ON subsite (alias_url);");

    },
       

    'verified seal migration' => function () use($conn){
        $agent_id = $conn->fetchColumn("select profile_id
                    from usr
                    where id = (
                        select min(usr_id)
                        from role
                        where name = 'superAdmin'
                    )");
	    $conn->executeQuery(
            "INSERT INTO seal VALUES(
                1,
                $agent_id,
                'Selo Mapas',
                'Descrição curta Selo Mapas','Descrição longa Selo Mapas',0,CURRENT_TIMESTAMP,1
            );"
        );
 	    $conn->executeQuery("INSERT INTO seal_relation
            SELECT nextval('seal_relation_id_seq'), 1, id, CURRENT_TIMESTAMP, 1, 'MapasCulturais\Entities\Agent', $agent_id FROM agent WHERE is_verified = 't';");
 	    $conn->executeQuery("INSERT INTO seal_relation SELECT nextval('seal_relation_id_seq'), 1, id, CURRENT_TIMESTAMP, 1, 'MapasCulturais\Entities\Space', $agent_id FROM space WHERE is_verified = 't';");
 	    $conn->executeQuery("INSERT INTO seal_relation SELECT nextval('seal_relation_id_seq'), 1, id, CURRENT_TIMESTAMP, 1, 'MapasCulturais\Entities\Project', $agent_id FROM project WHERE is_verified = 't';");
 	    $conn->executeQuery("INSERT INTO seal_relation SELECT nextval('seal_relation_id_seq'), 1, id, CURRENT_TIMESTAMP, 1, 'MapasCulturais\Entities\Event', $agent_id FROM event WHERE is_verified = 't';");
    },

    'create update timestamp entities' => function () use($conn) {
    	$conn->executeQuery("ALTER TABLE agent ADD COLUMN update_timestamp TIMESTAMP(0) WITHOUT TIME ZONE;");
    	$conn->executeQuery("ALTER TABLE space ADD COLUMN update_timestamp TIMESTAMP(0) WITHOUT TIME ZONE;");
    	$conn->executeQuery("ALTER TABLE project ADD COLUMN update_timestamp TIMESTAMP(0) WITHOUT TIME ZONE;");
    	$conn->executeQuery("ALTER TABLE event ADD COLUMN update_timestamp TIMESTAMP(0) WITHOUT TIME ZONE;");
    	$conn->executeQuery("ALTER TABLE seal ADD COLUMN update_timestamp TIMESTAMP(0) WITHOUT TIME ZONE;");
    },
    
    'alter table role add column subsite_id' => function () use($conn) {
    	$conn->executeQuery("ALTER TABLE role DROP CONSTRAINT IF EXISTS role_user_fk;");
    	$conn->executeQuery("ALTER TABLE role DROP CONSTRAINT IF EXISTS role_unique;");
        $conn->executeQuery("ALTER TABLE role ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE role ALTER id DROP DEFAULT;");
        $conn->executeQuery("ALTER TABLE role ALTER usr_id DROP NOT NULL;");
        $conn->executeQuery("ALTER TABLE role ADD CONSTRAINT FK_57698A6AC69D3FB FOREIGN KEY (usr_id) REFERENCES usr (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("ALTER TABLE role ADD CONSTRAINT FK_57698A6AC79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_57698A6AC79C849A ON role (subsite_id);");
    },
            
    'Fix field options field type from registration field configuration' => function () use($conn) {
        $conn->executeQuery("ALTER TABLE registration_field_configuration ALTER COLUMN field_options TYPE text;");
    },
    
    'ADD columns subsite_id' => function () use($conn) {
        $conn->executeQuery("ALTER TABLE space ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE space ADD CONSTRAINT FK_2972C13AC79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_2972C13AC79C849A ON space (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE agent ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DC79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_268B9C9DC79C849A ON agent (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE event ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_3BAE0AA7C79C849A ON event (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE project ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEC79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_2FB3D0EEC79C849A ON project (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE seal ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE seal ADD CONSTRAINT FK_2E30AE30C79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_2E30AE30C79C849A ON seal (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE registration ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7C79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_62A8A7A7C79C849A ON registration (subsite_id);");
        
        $conn->executeQuery("ALTER TABLE user_app ADD subsite_id INT DEFAULT NULL;");
        $conn->executeQuery("ALTER TABLE user_app ADD CONSTRAINT FK_22781144C79C849A FOREIGN KEY (subsite_id) REFERENCES subsite (id) NOT DEFERRABLE INITIALLY IMMEDIATE;");
        $conn->executeQuery("CREATE INDEX IDX_22781144C79C849A ON user_app (subsite_id);");
    },
    
    'remove subsite slug column' => function () use($conn) {
        $conn->executeQuery("ALTER TABLE subsite DROP COLUMN slug;");
    }
];
