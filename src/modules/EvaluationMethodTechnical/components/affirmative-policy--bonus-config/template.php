<?php

use MapasCulturais\i;

/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

$this->import('
    mc-icon
    mc-modal
    mc-select
');
?>


<div class="affirmative-policy--bonus-config">
    <div class="affirmative-policy--bonus-config__card" v-if="entity.affirmativePolicyBonusConfig">
        <div class="affirmative-policy--bonus-config__header">
            <h4 class="bold"><?= i::__('Configuração do Bônus de Pontuação') ?></h4>
            <div class="affirmative-policy--bonus-config__field field">
                <label>
                    <?= i::__('Percentual total de indução:') ?>
                </label>
                <span>
                    <input type="number" /> %
                </span>
            </div>
        </div>

        <div class="affirmative-policy--bonus-config__quota" v-for="(quota, index) in entity.affirmativePolicyBonusConfig.rules" :key="index">
            <div class="affirmative-policy--bonus-config__column">
                <h5 class="field__title--semibold"><?= i::__('Percentual') ?> {{index+1}}</h5>


                <mc-select @change-option="setFieldName($event, quota)">
                    <option v-for="(item, index) in entity.opportunity.affirmativePoliciesEligibleFields" :value="item.fieldName">{{ '#' + item.id + ' ' + item.title }}</option>
                </mc-select>

                <div class="affirmative-policy--bonus-config__fields">
                    <div class="field">
                        <label v-if="hasField(quota)">
                            <?= i::__('Tipo:') ?>
                        </label>
                    </div>
                    <div class="field affirmative-policy--bonus-config__row" v-if="getFieldType(quota) === 'select' || getFieldType(quota) === 'multiselect'">
                        <label v-for="option in getFieldOptions(quota)">
                            <input class="input" type="checkbox" :value="option" v-model="quota.eligibleValues" @change="autoSave()">
                            {{option}}
                        </label>
                    </div>

                    <div class="field__column" v-if="getFieldType(quota) === 'checkbox' || getFieldType(quota) === 'boolean'">
                        <label>
                            <input class="input" type="radio" :name="quota.fieldName + ':' + index" :value="true" v-model="quota.eligibleValues" @change="autoSave()">
                            <?= i::__('Sim / Marcado') ?>
                        </label>
                        <label>
                            <input class="input" type="radio" :name="quota.fieldName + ':' + index" :value="false" v-model="quota.eligibleValues" @change="autoSave()">
                            <?= i::__('Não / Desmarcado') ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="affirmative-policy--bonus-config__column">
                <label class="field"><?= i::__('Porcentagem') ?>
                    <div>
                        <input type="number" v-model="quota.percentage" @change="updateRuleQuotas(quota)" min="0" max="100"> %
                    </div>
                </label>
            </div>


            <div class="quota__trash">
                <mc-confirm-button @confirm="removeConfig(index)">
                    <template #button="{open}">
                        <div class="field__trash button button--md button--text-danger button-icon">
                            <mc-icon class="danger__color" name="trash" @click="open()"></mc-icon>
                        </div>
                    </template>
                    <template #message="message">
                        <?= i::__('Deseja deletar a cota?') ?>
                    </template>
                </mc-confirm-button>
            </div>
        </div>


    </div>
    <div class="affirmative-policy--bonus-config__footer">
        <button @click="addConfig();" class="button button--primary button--icon">
            <mc-icon name="add"></mc-icon>
            <label v-if="entity.affirmativePolicyBonusConfig && entity.affirmativePolicyBonusConfig.rules.length > 0">
                <?php i::_e("Adicionar categoria") ?>
            </label>
            <label v-else>
                <?php i::_e("Configurar Cotas por Categoria") ?>
            </label>
        </button>
    </div>
</div>