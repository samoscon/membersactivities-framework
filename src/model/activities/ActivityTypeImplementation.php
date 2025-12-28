<?php
/**
 * ActivityTypeImplementation.php
 *
 * @package membersactivities\model\activities
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\activities;

/**
 * Different types of ActivityTypeImplementation (e.g. RGLR, RUN, SWIM, etc.).
 * The default type is RGLR. The type is set on the basis of the value in the column classification.
 * 
 * Implementation of this class follows the OO design pattern 'Bridge'
 * 
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
abstract class ActivityTypeImplementation {
    //put your code here
    public function seatmap(): bool {
        return false;
    }
}
