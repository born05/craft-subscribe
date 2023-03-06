<?php

namespace born05\craftsubscribe\interfaces;

use yii\base\DynamicModel;

interface CrmInterface
{
    public function insert(DynamicModel $subscriber): array;
    public function upsert(DynamicModel $subscriber): array;
    public function createModel(array $params): DynamicModel;
}
