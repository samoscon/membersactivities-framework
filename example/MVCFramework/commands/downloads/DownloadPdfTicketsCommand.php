<?php
/**
 * Specialization of a Command
 *
 * @package commands\user
 * @version 4.0
 * @copyright (c) 2024, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace commands\downloads;
require_once './vendor/setasign/fpdf/fpdf.php';

/**
 * Specialization of a Command
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class DownloadPdfTicketsCommand extends \controllers\Command {
    //put your code here
    
    #[\Override]
    public function doExecute(\registry\Request $request): int {
        // Instantiation of inherited class
        $pdf = new PDF();
        $id = filter_var($request->get('id'), FILTER_VALIDATE_INT)/171963;
        try {
            $payment = \model\Payment::find($id);

//            $title = date('d/m/Y', strtotime($payment->date)).'_'.$payment->getId();
            $title = 'Tickets ' . APP . ' orderid '.$payment->getId();
            $pdf->SetTitle($title, true);
            // Column headings
//            $header = array('Naam', 'Gemeente', 'GSM', 'Inschrijving', 'Aantal');
            // Data loading
//            $data = $pdf->LoadData($id, $this->reg->getDb());

            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Times','B',12);
            
            $subscriptions = \model\Subscription::findAll("WHERE payment_id = ".$payment->getId());
            $total = 0;
            $numberOfSubscriptions = 0;
            $tickets = [];
            foreach ($subscriptions as $subscription) {
                $total += $subscription->quantity*$subscription->costitem->price;
                for($x=0; $x<$subscription->quantity; $x++) {
                    $tickets[] = $subscription->costitem->description .' ('.$subscription->costitem->price.' EUR)';
                }
                $numberOfSubscriptions += $subscription->quantity;
            }
            $activity=$subscription->costitem->activity;
            $pdf->Cell(0,10,'Concert: '.$activity->description,0,1,'L');
            $pdf->Ln(5);
            
            if(!$activity->isComposite()) {
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(20,5,'When:',0,0,'L');
            $pdf->SetFont('Times','',10);
            $pdf->Cell(20,5,date('d/m/Y', strtotime($activity->date)),0,0,'L');
            $pdf->Cell(0,5,('('.substr($activity->start,0,5).' - '.substr($activity->end,0,5).')'),0,1,'L');

            $pdf->SetFont('Times','B',10);
            $pdf->Cell(20,5,'Where:',0,0,'L');
            $pdf->SetFont('Times','',10);
            $pdf->Cell(0,5,$activity->location,0,1,'L');
            } else {
            $pdf->SetFont('Times','',10);
            $pdf->MultiCell(0,5,(str_replace("&nbsp;", '',strip_tags($activity->longdescription))),0,'L');
            }
            $pdf->Ln(5);


            $pdf->SetFont('Times','B',10);
            $pdf->Cell(20,5,'Ordered by:',0,0,'L');
            $pdf->SetFont('Times','',10);
            $pdf->Cell(0,5,$subscription->member->name.' ('.$subscription->member->email.')',0,1,'L');
            $pdf->Ln(5);

            require_once './vendor/autoload.php';

            $options = new \chillerlan\QRCode\QROptions;

            $options->outputBase64     = true;
            $options->outputType = \chillerlan\QRCode\Output\QROutputInterface::GDIMAGE_PNG;

                       
            $pdf->SetFont('Times','B',12);
            $pdf->Cell(20,10,'Your tickets:',0,0,'L');
            $pdf->SetFont('Times','',10);
            $pdf->Ln(10);
            for($i=0;$i<$numberOfSubscriptions;$i++) {
                $data   = $activity->date . ' - '.$activity->description . ' - '. $tickets[$i].' - '.($i+1).' of '.$numberOfSubscriptions . ' - Subscriptionid:'. $subscription->getId();
                $qrcode = (new \chillerlan\QRCode\QRCode($options))->render($data, 'assets/qrcodes/test'.$i.'.png');
                $pdf->Cell(0,10,($i+1).'. '.$tickets[$i],0,1,'L');
                $pdf->Image('assets/qrcodes/test'.$i.'.png', null, null, 36);
                $pdf->Ln(5);
            }
//            $pdf->FancyTable($header, $data);
            $pdf->Output('D', $title . ".pdf");
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }
        
        return self::CMD_DEFAULT;        
    }
    
    #[\Override]
    protected function getLevelOfLoginRequired(): void {
        $this->setLoginLevel(new \sessions\NoLoginRequired());        
    }
}

/**
 * Subclass of the FPDF class/framework. Provides the layout for the PDF file output.
 */
class PDF extends \FPDF
{
    /**
     * Page Header
     */
    function Header()
    {
        $title = $this->metadata['Title'];

        // Logo
        $this->Image(_LOGO,10,6,32);
        // Arial bold 15
//        $this->SetFont('Arial','B',15);
        // Calculate width of title and position
//        $w = $this->GetStringWidth($title)+6;
//        $this->SetX((210-$w)/2);
        // Colors of frame, background and text
//        $this->SetDrawColor(25,157,256);
//        $this->SetFillColor(0,0,0);
//        $this->SetTextColor(255);
        // Thickness of frame (1 mm)
//        $this->SetLineWidth(0.1);
        // Title
         //$this->Cell($w,9,$title,1,1,'C',true);
        // Line break
        //$this->Ln(10);
    }

    /**
     * Page footer
     */
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }    
}
