<?php
/**
 * Specialization of a Command
 *
 * @package commands\admin
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\viewrender\admin;

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class EditCostitemCommand extends \controllers\Command {
    
    /**
     * Specialization of the execute method of Command
     * 
     * @param \registry\Request $request
     * @return int
     */
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        /** Variables */
        $descriptionIsEmpty = $priceIsEmpty = false;
        $properties = array();
        
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT);
        if(!$id) {
            $request->addFeedback("Geen correct id opgegeven.");
            return self::CMD_ERROR;
        }
        
        try {
            $costitem = \model\Costitem::find($id);
        } catch (\Exception $exc) {
            $request->addFeedback($exc->getMessage());
            return self::CMD_ERROR;
        }

        /** Check that the page was requested from itself via the POST method. */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $properties['description'] = $description = filter_var($request->get('description'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descriptionIsEmpty = $description ? false : true;
            
            $price = filter_var($request->get('price'), FILTER_VALIDATE_FLOAT);
            if(!$price){
                if($request->get('price') == "0"){
                    $price = 0.00;
                } else {
                    $priceIsEmpty = true;
                }
            }
            $properties['price'] = $price; 
            
            if (!$descriptionIsEmpty && !$priceIsEmpty) {
                $costitem->update($properties);
                
                $request->set('forwardqueryparams', ['id' => $costitem->activity_id]);
                return self::CMD_OK;
            }
        } 
            
        /** the page was requested via the GET method or the POST method did not return a status. */
        $responses['costitem'] = $costitem;        
        $responses['returnpath'] = 'editActivity?id='.$costitem->activity_id;        
        $this->addResponses($request, $responses);
        return self::CMD_DEFAULT;
    }

    /**
     * Specialization of getLevelOfLoginRequired
     */
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());
    }

}