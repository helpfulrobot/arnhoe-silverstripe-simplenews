<?php
/**
 *
 * @package arnhoe/silverstripe-simplenews
 * @author Arno Poot <mail@arnop.nl>
 *
 */
class NewsArticle extends DataObject
{

    private static $singular_name = "News article";
    public function i18n_singular_name()
    {
        return _t("NewsArticle.Singular", $this->stat("singular_name"));
    }
    private static $plural_name = "News articles";
    public function i18n_plural_name()
    {
        return _t("NewsArticle.Plural", $this->stat("plural_name"));
    }

    private static $default_sort = "Date DESC";

    private static $db = array(
        "Title" => "Varchar(255)",
        "Date" => "Datetime",
        "Content" => "HTMLText"
    );

    private static $has_one = array(
        "NewsHolder" => "NewsHolder",
        "NewsImage" => "Image"
    );

    private static $summary_fields = array(
        "Title",
        "Date"
    );

    private static $required_fields = array(
        "Title",
        "Date",
        "Content"
    );

    public function getCMSFields()
    {
        $datetimeField = DatetimeField::create("Date")->setTitle($this->fieldLabel("Date"));
        $datetimeField->getDateField()->setConfig("dmyfields", true);

        // Check if NewsImage should be saved in a seperate folder
        if (self::config()->save_image_in_seperate_folder == false) {
            $UploadField = UploadField::create("NewsImage")->setTitle($this->fieldLabel("NewsImage"))->setFolderName("news");
        } else {
            if ($this->ID == "0") {
                $UploadField = FieldGroup::create(
                    LiteralField::create("Save", $this->fieldLabel("SaveHelp"))
                        )->setTitle($this->fieldLabel("NewsImage"));
            } else {
                $UploadField = UploadField::create("NewsImage")->setTitle($this->fieldLabel("NewsImage"))->setFolderName("news/".$this->URLSegment);
            }
        }

        // Create direct link to NewsArticle
        if ($this->ID == "0") {
            // Little hack to hide $urlsegment when article isn't saved yet.
            $urlsegment = LiteralField::create("NoURLSegmentYet", "");
        } else {
            if ($NewsHolder = $this->NewsHolder()) {
                $baseLink = Controller::join_links(
                    Director::absoluteBaseURL(), $NewsHolder->Link(), $this->URLSegment
                );
            }
            $urlsegment = Fieldgroup::create(
                LiteralField::create("URLSegment", "URLSegment")->setContent('<a href="'.$baseLink.'" target="_blank">'.$baseLink.'</a>')
            )->setTitle("URLSegment");
        }

        $fields = FieldList::create(
            new TabSet("Root",
                new Tab("Main",
                    $urlsegment,
                    TextField::create("Title")->setTitle($this->fieldLabel("Title")),
                    $datetimeField,
                    HTMLEditorField::create("Content")->setTitle($this->fieldLabel("Content")),
                    $UploadField
                )
            )
        );
        $this->extend("updateCMSFields", $fields);
        return $fields;
    }

    public function populateDefaults()
    {
        parent::populateDefaults();
        $this->Date = date("Y-m-d H:i");
    }

    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels($includerelations);

        $labels["Title"] = _t("NewsArticle.FieldTitle", "Title");
        $labels["Date"] = _t("NewsArticle.FieldDate", "Date");
        $labels["Content"] = _t("NewsArticle.FieldContent", "Content");
        $labels["NewsImage"] = _t("NewsArticle.FieldNewsImage", "News image");
        $labels["SaveHelp"] = _t("NewsArticle.SaveHelp", "Files can be attached once you have saved the record for the first time.");

        return $labels;
    }

    public function validate()
    {
        $result = parent::validate();

        foreach (self::$required_fields as $field) {
            if (empty($this->$field)) {
                $result->error(_t("SiteTree.FieldRequired",
                    "'{field}' is required",
                        array("field" => $field)
                ));
            }
        }

        return $result;
    }

    public function Link()
    {
        if ($NewsHolder = $this->NewsHolder()) {
            return Controller::join_links(
                $NewsHolder->Link(), $this->URLSegment
            );
        }
    }

    public function AbsoluteLink()
    {
        return Director::absoluteURL($this->Link());
    }
}
