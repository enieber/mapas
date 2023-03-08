<?php
use MapasCulturais\i;
$this->layout = 'entity';

$this->import('
    entity-field
    opportunity-form-builder-category
')
?>

<div class="form-builder__content">
    <div class="grid-12 form-builder__bg-content">
        <div class="col-8 sm:col-12 form-builder__title">
            <p class="opportunity__color"><?= i::__("1. {{ getTitleForm }}") ?></p>
        </div>
        <div class="col-2 sm:col-6 form-builder__period">
            <h5 class="period_label"><?= i::__("Data de início") ?></h5>
            <h5 class="opportunity__color">{{ getDateRegistrationFrom }}</h5>
        </div>
        <div class="col-2 sm:col-6 form-builder__period">
            <h5 class="period_label"><?= i::__("Data final") ?></h5>
            <h5 class="opportunity__color">{{ getDateRegistrationTo }}</h5>
        </div>
    </div>

    <div class="grid-12 form-builder__label-btn">
        <div class="col-12">
            <h3><?= i::__("Configuração de formulário de coleta de dados") ?></h3>
        </div>
    </div>

    <div class="grid-12">
        <div class="col-6 sm:col-12">
            <opportunity-form-builder-category v-if="entity.isFirstPhase || !entity.parent" :entity="entity"></opportunity-form-builder-category>
        </div>
        <div class="col-6 sm:col-12">
            <div class="form-builder__bg-content form-builder__bg-content--spacing">
                <div>
                    <h4><?= i::__("Permitir Agente Coletivo?") ?></h4>
                    <span class="subtitle"><?= i::__("Permitir inscrição de Agente Coletivo") ?></span>
                    <div>
                        <input type="radio" value="use" v-model="entity.useAgentRelationColetivo">
                        <label for="html"><?= i::__("Sim") ?></label>
                        <input type="radio" value="dontUse" v-model="entity.useAgentRelationColetivo">
                        <label for="css"><?= i::__("Não") ?></label>
                    </div>
                </div>
                <div>
                    <h4><?= i::__("Permitir instituição responsável?") ?></h4>
                    <span class="subtitle"><?= i::__("Permitir inscrição de instituições") ?></span>
                    <div>
                        <input type="radio" value="use" v-model="entity.useAgentRelationInstituicao">
                        <label for="html"><?= i::__("Sim") ?></label>
                        <input type="radio" value="dontUse" v-model="entity.useAgentRelationInstituicao">
                        <label for="css"><?= i::__("Não") ?></label>
                    </div>
                </div>
                <div>
                    <entity-field :entity="entity" prop="registrationLimit"></entity-field>
                </div>
                <div>
                    <entity-field :entity="entity" prop="registrationLimitPerOwner"></entity-field>
                </div>
            </div>
        </div>
        <div class="col-6 sm:col-12">
            <div class="form-builder__bg-content form-builder__bg-content--spacing">
                <h4><?= i::__("Permitir vínculo de Espaço?") ?></h4>
                <span class="subtitle"><?= i::__("Permitir um espaço para associar à inscrição.") ?></span>
                <div>
                    <input type="radio" value="use" v-model="entity.useSpaceRelationIntituicao">
                    <label for="html"><?= i::__("Sim") ?></label>
                    <input type="radio" value="dontUse" v-model="entity.useSpaceRelationIntituicao">
                    <label for="css"><?= i::__("Não") ?></label>
                </div>
            </div>
        </div>
        <div class="col-6 sm:col-12">
            <div class="form-builder__bg-content form-builder__bg-content--spacing">
                <h4><?= i::__("Habilitar informações de Projeto?") ?></h4>
                <span class="subtitle"><?= i::__("Permitir que proponente vizualise informações básicas sobre um projeto.") ?></span>
                <div>
                    <input type="radio" value="0" v-model="entity.projectName">
                    <label for="html"><?= i::__("Sim") ?></label>
                    <input type="radio" value="1" v-model="entity.projectName">
                    <label for="css"><?= i::__("Não") ?></label>
                </div>
            </div>
        </div>
    </div>
</div>