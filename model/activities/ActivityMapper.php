<?php
/**
 * ActivityMapper.php
 *
 * @package model\activities
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace model\activities;

/**
 * Instantiation of the DB Mapper for Activity class
 * 
 * Has to be further subclassed for application specific behavior
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
abstract class ActivityMapper extends \db\Mapper {    
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
     * @param Array $row
     * @return \model\Activity
     */
    #[\Override]
    protected function doCreateObject(string $classname, array $row): \model\activities\Activity {
        return $classname::getInstance($row);
    }
    
    public function getChildren(\model\activities\ActivityComposite $activitycomposite): \db\ObjectMap {
        $result = $this->checkForChildren($activitycomposite->getId());
        
        $activitychildren = new \db\ObjectMap();
        foreach ($result as $row) {
            $activity = \model\Activity::find($row['id']);
            $activitychildren->attach($activity, $row['id']);
        }
        return $activitychildren;
    }
    
    /**
     * Returns the subscribed members of the Activity $obj
     * 
     * @param \model\Activity $obj
     * @return \db\ObjectMap of \members\Member
     */
    public function getParticipants(\model\Activity $obj) {
        $sql = $this->db->prepare("SELECT DISTINCT member.id, name, lastname "
            . "FROM member, subscription, costitem "
            . "WHERE member.id = subscription.member_id AND subscription.`costItem_id` = costitem.id "
            . "AND costitem.activity_id = ? ORDER BY member.name, member.lastname");
        $sql->execute([$obj->getId()]);
        $result = $sql->fetchAll();
        $sql->closeCursor();

        $participants = new \db\ObjectMap();
        $reg = \registry\Registry::instance();
        $memberMapper = $reg->getMemberMapper();
        foreach ($result as $row) {
            $member = $memberMapper->find($row['id']);
            $participants->attach($member, $row['id']);
        }
        return $participants;
    }
}