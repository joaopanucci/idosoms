<?php
// config/constants.php

// Perfis de acesso
const ROLES = [
    'super_admin',
    'admin_estadual',
    'coord_municipal',
    'profissional',
];

// Classificação (PLACEHOLDER) — ajuste conforme norma oficial dos instrumentos
// Você pode centralizar as regras aqui e aplicar no servidor para evitar adulteração do valor do cliente.
const IVCF20_CLASSIFICATION = [
    ['min' => 0,  'max' => 6,  'label' => 'Robusto'],
    ['min' => 7,  'max' => 14, 'label' => 'Pré-frágil'],
    ['min' => 15, 'max' => 40, 'label' => 'Frágil'],
];

const IVSF10_CLASSIFICATION = [
    ['min' => 0,  'max' => 3,  'label' => 'Baixa vulnerabilidade social-funcional'],
    ['min' => 4,  'max' => 6,  'label' => 'Moderada vulnerabilidade social-funcional'],
    ['min' => 7,  'max' => 10, 'label' => 'Alta vulnerabilidade social-funcional'],
];


// Prefixo IBGE do Estado (MS = 50*).
// Use para travar escopo por Estado quando "admin_estadual".
const STATE_IBGE_PREFIX = '50';


/**
 * UF do administrador estadual (para escopo automático por Estado).
 * Ajuste para 'MS', 'SP', 'RJ', etc.
 */
const ADMIN_STATE_UF = 'MS';

/**
 * Mapeamento UF -> prefixo IBGE (automaticamente derivado para consulta).
 * Fontes oficiais indicam que os 2 primeiros dígitos são o código do Estado.
 */
const IBGE_UF_PREFIX = [
  'RO' => '11', 'AC' => '12', 'AM' => '13', 'RR' => '14',
  'PA' => '15', 'AP' => '16', 'TO' => '17', 'MA' => '21',
  'PI' => '22', 'CE' => '23', 'RN' => '24', 'PB' => '25',
  'PE' => '26', 'AL' => '27', 'SE' => '28', 'BA' => '29',
  'MG' => '31', 'ES' => '32', 'RJ' => '33', 'SP' => '35',
  'PR' => '41', 'SC' => '42', 'RS' => '43',
  'MS' => '50', 'MT' => '51', 'GO' => '52', 'DF' => '53'
];
