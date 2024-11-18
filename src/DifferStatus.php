<?php

namespace Differ\DifferStatus;

enum DiffStatus: string
{
    case added = 'added';
    case removed = 'removed';
    case updated = 'updated';
    case noDifference = 'noDifference';
    case parentDiffNode = 'parentDiffNode';
}
