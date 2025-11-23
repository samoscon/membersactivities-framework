<?php
/**
 * ActivityMapper.php
 *
 * @package membersactivities\model\activities
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace membersactivities\model\activities;

use controllerframework\db\ObjectMap;

/**
 * Instantiation of the DB Mapper for Activity class
 * 
 * Has to be further subclassed for application specific behavior
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class ActivityMapper extends \controllerframework\db\Mapper {    
    /**
     * @var string Contains the name of the related table to the Activity class(es) in the database
     */
    private string $tablename = 'activity';
    
    /**
     * Returns the associated tablename for the Activity class; i.e. activity
     * 
     * @return string
     */
    #[\Override]
    public function tablename(): string {
        return $this->tablename;
    }
    
    /**
     * Returns on the basis of a database row the associated object
     * 
     * @param string $classname Name of the class
     * @param array $row Database row
     * @return Activity Returns the object found on the basis of database row
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row):   Activity {
        return $classname::getInstance($row);
    }
    
    /**
     * Returns an ObjectMap of Activities that belongs to this Composite
     * 
     * @return ObjectMap of Activities
     */
    public function getChildren(ActivityComposite $activitycomposite): ObjectMap {
        $result = $this->checkForChildren($activitycomposite->getId());
        
        $activitychildren = new ObjectMap();
        foreach ($result as $row) {
            $activity = \model\Activity::find($row['id']);
            $activitychildren->attach($activity, $row['id']);
        }
        return $activitychildren;
    }
    
    /**
     * Returns the subscribed members of the Activity $obj
     * 
     * @param Activity $obj
     * @param ObjectMap $participants
     * @return ObjectMap of Members
     */
    public function getParticipants(Activity $obj, ObjectMap $participants): ObjectMap {
        $sql = $this->db->prepare("SELECT DISTINCT member.id, name, email, costitem.description AS costitemdescription, quantity "
            . "FROM member, subscription, costitem, payment "
            . "WHERE member.id = subscription.member_id AND subscription.`costItem_id` = costitem.id "
            . "AND costitem.activity_id = ? AND status = 'paid' ORDER BY member.name, member.lastname");
        $sql->execute([$obj->getId()]);
        $result = $sql->fetchAll();
        $sql->closeCursor();

        foreach ($result as $row) {
            $member = \model\Member::find($row['id']);
            $member->costitem = $row['costitemdescription'];
            $member->quantity = $row['quantity'];
            $participants->attach($member, $row['id']);
        }
        return $participants;
    }
}
