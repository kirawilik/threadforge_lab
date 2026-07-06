<?php

namespace App\Enums;

enum PostStatus:string
{
  
    case Pending = 'pending';
    case Draft = 'draft';
    case Posted = 'posted';
    case Archived = 'archived';

}
