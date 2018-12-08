<?php

namespace smart\base;

interface FilterInterface
{

    /**
     * Search function
     * @param array|null $config Data provider config
     * @return yii\data\ActiveDataProvider
     */
    public function getDataProvider($config = []);

}
