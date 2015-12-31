<?php
/**
 *
 * @package arnhoe/silverstripe-simplenews
 * @author Arno Poot <mail@arnop.nl>
 *
 */
class NewsHolder extends Page
{

    private static $description = "News holder for news articles";

    private static $db = array(
        "NewsPageLimit" => "Int(5)"
    );

    private static $has_many = array(
        "NewsArticles" => "NewsArticle"
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab("Root.News", NumericField::create("NewsPageLimit")->setTitle(_t("NewsHolder.NewsPageLimit", "Page limit")));

        $NewsGridFieldConfig = GridFieldConfig_RecordEditor::create();

        $NewsArticleGrid = GridField::create(
            "NewsArticles",
            _t("NewsHolder.NewsArticlesTitle", "News articles"),
            $this->NewsArticles(),
            $NewsGridFieldConfig
        );

        $fields->addFieldToTab("Root.News", $NewsArticleGrid);

        return $fields;
    }
}

class NewsHolder_Controller extends Page_Controller
{

    private static $allowed_actions = array(
        "newsarticle", "rss"
    );

    private static $url_handlers = array(
        'rss' => 'rss',
        '$NewsURLSegment!' => 'newsarticle'
    );

    public function init()
    {
        RSSFeed::linkToFeed($this->Link() . "rss");
        parent::init();
    }

    public function newsarticle($request)
    {
        $NewsLink = $request->param("NewsURLSegment");
        $NewsArticle = NewsArticle::get()->filter(array("URLSegment" => $NewsLink))->First();

        if (!$NewsArticle) {
            $this->httpError(404);
        }

        return $this->customise(array("NewsArticle" => $NewsArticle, "Title" => $NewsArticle->Title))->renderWith(array("NewsArticle", "Page"));
    }

    public function rss()
    {
        $SiteConfig = SiteConfig::current_site_config();
        $rss = new RSSFeed(NewsArticle::get(), $this->Link(), $SiteConfig->Title);
        return $rss->outputToBrowser();
    }

    public function PaginatedNewsArticles()
    {
        return PaginatedList::create($this->NewsArticles(), $this->request)->setPageLength($this->NewsPageLimit);
    }
}
