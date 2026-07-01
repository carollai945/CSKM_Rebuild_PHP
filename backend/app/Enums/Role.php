<?php

namespace App\Enums;

enum Role: string
{
    case CEO     = 'ceo';
    case RegMgr  = 'regmgr';
    case Staff   = 'staff';
    case Teacher = 'teacher';
    case Finance = 'finance';
    case Admin   = 'admin';
}
