<?php

namespace smart\mappers;

use yii\helpers\HtmlPurifier;

class HtmlMapper extends Mapper
{
    /**
     * @inheritdoc
     */
    protected function assignFromValue($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    protected function assignToValue($value)
    {
        return HtmlPurifier::process($value, function($config) {
            $config->set('Attr.EnableID', true);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(?:https?:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/|yandex.ru/map-widget/)%');
        });
    }
}
