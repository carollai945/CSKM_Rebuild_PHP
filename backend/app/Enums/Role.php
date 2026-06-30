<?php

namespace App\Enums;

enum Role: string
{
    case CEO     = 'CEO';
    case RegMgr  = 'REG_MGR';
    case Staff   = 'STAFF';
    case Teacher = 'TEACHER';
    case Finance = 'FINANCE';
    case Admin   = 'ADMIN';

    public function label(): string
    {
        return match($this) {
            self::CEO     => '系統管理員',
            self::RegMgr  => '區域主管',
            self::Staff   => '學顧',
            self::Teacher => '教務人員',
            self::Finance => '財務人員',
            self::Admin   => '管理部人員',
        };
    }

    /** Roles that can manage system master data */
    public static function masterDataWriters(): array
    {
        return [self::CEO, self::Admin];
    }

    /** Roles that can approve applications */
    public static function approvers(): array
    {
        return [self::CEO, self::RegMgr, self::Admin];
    }
}
