<?php
/**
 * Activity_STMP.php
 *
 * @package model
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model;

/**
 * Represents an Activity with a sitemap to be included in the subscription.
 * 
 * Implementation follows the design pattern 'Builder'
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Activity_STMP  extends \membersactivities\model\activities\ActivityTypeImplementation {
    //put your code here
    public function seatmap(): bool {
        return true;
    }
}
