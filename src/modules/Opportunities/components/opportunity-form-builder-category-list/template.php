<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->layout = 'entity';

$this->import('
    mc-icon 
    mc-popover 
    mc-tag-list
');
?>
<div class="build-list">
    <h6 class="build-list__title"><?= i::__("Categorias") ?></h6>

    <mc-tag-list class="build-list__content" :tags="entity.registrationCategories" editable></mc-tag-list>

    <mc-popover class="popover-tag" openside="down-right" title="<?php i::esc_attr_e('Adicionar categoria') ?>">
        <template #button="popover">
            <button @click="popover.toggle()" class="button-popover button button--primary button--icon button--primary-outline">
                <mc-icon name="add"></mc-icon><label><?php i::_e("Adicionar categoria") ?></label>
            </button>
        </template>

        <template #default="{close}">
            <form @submit="addCategory(); $event.preventDefault(); close(); clear()" class="entity-links__newLink">
                <div class="grid-12">
                    <div class="col-12">
                        <div class="field">
                            <label><?php i::_e('Descrição da categoria') ?></label>
                            <input v-model="category" class="newCategoryAdded" type="text" name="newCategoryAdded" />
                        </div>
                    </div>

                    <button class="col-6 button button--text" type="reset" @click="close"> <?php i::_e("Cancelar") ?> </button>
                    <button class="col-6 button button--primary" type="submit"> <?php i::_e("Confirmar") ?> </button>
                </div>
            </form>
        </template>
    </mc-popover>
</div>