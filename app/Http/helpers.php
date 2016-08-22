<?php
/**
 * Helper File Doc Comment
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Laravel
 * @author   Ryan Prader <raiprads@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://localhost
 */

use App\Linkedevent;
use App\Bookmark;


/**
* Return a diable status for button
*
* @param string $action    'favorite','wish','watch'
* @param string $listingId Listing Id from helsinki api
*
* @return string
*/
function disableBookmarkButton($action, $listingId)
{

    if (Auth::check()) {
        $user_id = Auth::User()->id;
        $bookmark = new Bookmark;        
        $linkedevent = new Linkedevent;
        $linkedevent = $bookmark->checkEvent($listingId);

        if (!is_null($linkedevent)) {
            $count = $bookmark->checkExistingBookmark(
                array(
                'user_id'=> $user_id,
                'linkedevent_id'=> $linkedevent->id,
                'action'=> $action
                )
            );
            if ($count>0) {
                return ' disabled ';
            }
        }

    }

}


/**
* Show number of bookmarks in the buttons
*
* @param string $action    'favorite','wish','watch'
* @param string $listingId Listing Id from helsinki api
*
* @return string
*/
function showNumberOfBookmarks($action, $listingId)
{
    $bookmark = new Bookmark;        
    $linkedevent = new Linkedevent;
    $linkedevent = $bookmark->checkEvent($listingId);
    $count = 0 ;

    if (!is_null($linkedevent)) {
        $count = $bookmark->getNumberOfBookmarks(
            array(
            'linkedevent_id'=> $linkedevent->id,
            'action'=> $action
            )
        );
    }

    return $bookmark->getButtonLabel($action, $count);

}
