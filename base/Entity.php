<?php

namespace smart\base;

use smart\db\ActiveRecord;

/**
 * Entity is the endpoint in the site tha have own address in the web.
 * SEO and Sitemap is worked with entities.
 */
class Entity extends ActiveRecord
{
    /**
     * @event Event an event that is triggered when sitemap entity update needed.
     */
    const EVENT_SITEMAP_UPDATE = 'sitemapUpdate';
    /**
     * @event Event an event that is triggered when sitemap entity delete needed.
     */
    const EVENT_SITEMAP_DELETE = 'sitemapDelete';

    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    /**
     * Check that entity is active
     * @return boolean
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Page title
     * @return string|null
     */
    public function pageTitle()
    {
        return null;
    }

    /**
     * Meta keywords tag content
     * @return string|null
     */
    public function metaKeywords()
    {
        return null;
    }

    /**
     * Meta keywords tag content
     * @return string|null
     */
    public function metaDescription()
    {
        return null;
    }

    /**
     * URL of the page. This URL must begin with the protocol (such as http) and end with a trailing slash, if your web server requires it. This value must be less than 2,048 characters.
     * @return string
     */
    public function sitemapLoc()
    {
        return '';
    }

    /**
     * The date of last modification of the file. This date should be in W3C Datetime format. This format allows you to omit the time portion, if desired, and use YYYY-MM-DD.
     * If not set, will be generated automatically.
     * @return string|null
     */
    public function sitemapLastmod()
    {
        return null;
    }

    /**
     * How frequently the page is likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page.
     * @return string
     */
    public function sitemapChangefreq()
    {
        return self::CHANGEFREQ_NEVER;
    }

    /**
     * The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0. 
     * This value does not affect how your pages are compared to pages on other sites—it only lets the search engines know which pages you deem most important for the crawlers.
     * @return float
     */
    public function sitemapPriority()
    {
        return 0.5;
    }

    /**
     * {@inheritdoc}
     * Attach events for sitemap functionality
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'entityOnAfterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'entityOnAfterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'entityOnAfterDelete',
        ];
    }

    /**
     * After save event
     * @param yii\base\Event $event 
     * @return void
     */
    public function entityOnAfterSave($event)
    {
        $this->trigger($this->isActive() ? self::EVENT_SITEMAP_UPDATE : self::EVENT_SITEMAP_DELETE, $event);
    }

    /**
     * After delete event
     * @param yii\base\Event $event 
     * @return void
     */
    public function entityOnAfterDelete($event)
    {
        $this->trigger(self::EVENT_SITEMAP_DELETE, $event);
    }
}
