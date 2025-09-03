<?php
// src/Core/Scope.php
namespace Core;

class Scope {
    // Returns [sqlSnippet, paramsArray] to enforce data scope by role.
    public static function whereForUser(array $u): array {
        $role = $u['role'] ?? '';
        $params = [];
        $parts = [];
        if ($role === 'profissional') {
            $parts[] = 'e.created_by = ?';
            $params[] = (int)$u['id'];
        } elseif ($role === 'coord_municipal') {
            if (!empty($u['municipality_code'])) {
                $parts[] = 'p.municipality_code = ?';
                $params[] = $u['municipality_code'];
            } else {
                // fallback: none if no municipality bound — returns no rows
                $parts[] = '1=0';
            }
        }
        // admin_estadual: restringe por prefixo IBGE do estado (se definido)
        elseif ($role === 'admin_estadual') {
            if (defined('STATE_IBGE_PREFIX') && STATE_IBGE_PREFIX !== '') {
                $parts[] = 'p.municipality_code LIKE ?';
                $params[] = STATE_IBGE_PREFIX . '%';
            }
        }
        // super_admin: sem restrição adicional
        $sql = $parts ? ('(' . implode(' AND ', $parts) . ')') : '';
        return [$sql, $params];
    }
}
