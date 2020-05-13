<?php

namespace App\Http\Controllers\Admin;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PayrollUpload;
use A17\Twill\Repositories\SettingRepository;
use App\Mail\UploadNotification;
use Illuminate\Support\Facades\Mail;

class PayrollUploadController extends ModuleController
{
    protected $moduleName = 'payrollUploads';

    /*
     * Options of the index view
     */
    protected $indexOptions = [
        'create' => false,
        'edit' => false,
        'publish' => false,
        'bulkPublish' => false,
        'feature' => false,
        'bulkFeature' => false,
        'restore' => false,
        'bulkRestore' => false,
        'delete' => false,
        'bulkDelete' => false,
        'reorder' => false,
        'permalink' => false,
        'bulkEdit' => false,
        'editInModal' => false,
        'forceDelete' => false,
        'bulkForceDelete' => false,
        'duplicate' => false
    ];

   /*
     * Available columns of the index view
     */
    protected $indexColumns = [
        'title' => [ // field column
            'title' => 'Name',
            'field' => 'title',
        ],
        'uf_link' => [
            'title' => 'Download XML File',
            'field' => 'uf_link',
            'visible' => true,
        ],
        'created_at' => [
            'title' => 'Created At',
            'field' => 'created_at',
            'sort' => true,
            'visible' => true,
        ],
    ];


    protected $valid = true;
    protected $lineCode = "";
    protected $linecount = 0;
    protected $payeeCount = 0;
    protected $batchline = 0;
    protected $line1 = false;
    protected $line5 = false;
    protected $line6 = false;
    protected $line8 = false;
    protected $line9 = false;
    protected $totalCredits = 0;
    protected $tempCredits = 0;
    protected $DFIRouting = "";
    protected $DFIAccount = "";
    protected $TranCode = "";
    protected $identNum = "";
    protected $identName = "";

    public function upload(Request $request){
        $dt = Carbon::now();
        $file = $request->file('filepond');

        $fileName = 'gipson-'.$dt->format('Ymd').$dt->timestamp.'.ach';
        $originalFile = Storage::putFileAs('public/payroll', $file, $fileName);

        $dom = new \DOMDocument('1.0'); 
        $root = $dom->createElement('query'); 
        $root->setAttribute('xmlns', 'http://www.corelationinc.com/queryLanguage/v1.0');
        $root->setAttribute('fileDate', $dt->format('Y-m-d'));
        $sequence = $dom->createElement('sequence');        
        $transaction = $dom->createElement('transaction');

        $content = explode("\n", $file->get());

        foreach($content as $line) {
            $this->linecount++;
            $lineCode = Str::substr($line, 0, 1);

            switch ($lineCode) {
                case 1:
                    $this->line1 = true;
                    if ($this->linecount != 1)
                        $this->valid = false;
                    break;
                case 5:
                    $this->line5 = true;
                    if ($this->linecount != 2)
                        $this->valid = false;
                    break;
                case 6:
                    $this->line6 = true;
                    $this->payeeCount++;
                    if ($this->linecount < 3)
                        $this->valid = false;
                    if ($this->linecount < $this->batchline && $this->batchline != 0)
                        $this->valid=false;

                    $this->tempCredits = (int)Str::substr($line, 29, 10);
                    if ($this->tempCredits)
                    {
                        $this->totalCredits += $this->tempCredits;
                        $this->TranCode = Str::substr($line, 1, 2);
                        $this->DFIAccount = trim(Str::substr($line, 12, 17));
                        $this->DFIRouting = Str::substr($line, 3, 9);
                        $this->identNum = trim(Str::substr($line, 39, 15));
                        $this->identName = trim(Str::substr($line, 54, 22));
                    }
                    if (Str::substr($line, 2, 1) != "2")
                        $this->valid=false;

                    $step = $dom->createElement('step'); 
                    $record = $dom->createElement('record'); 

                    $operation = $dom->createElement('operation');
                    $operation->setAttribute('option', 'I');
                    $record->appendChild($operation);

                    $tableName = $dom->createElement('tableName', 'ACH_OUT_ENTRY');
                    $record->appendChild($tableName);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'ITEM_GL_SERIAL');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', '3690');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'STANDARD_ENTRY_CLASS_CODE');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', 'PPD');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'COMPANY_NAME');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', 'Gipson\'s Auto Ti');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'COMPANY_ID');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', '1630936312');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'COMPANY_ENTRY_DESCRIPTION');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', 'PAYROLL');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'TRANSACTION_CODE');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', $this->TranCode);
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'DFI_ROUTING_NUMBER');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', $this->DFIRouting);
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'DFI_ACCOUNT_NUMBER');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', $this->DFIAccount);
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'AMOUNT');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', number_format(floatval($this->tempCredits)/100, 2, '.', ''));
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'IDENTIFICATION_NUMBER');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', $this->identNum);
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'NAME');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', $this->identName);
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $field = $dom->createElement('field'); 
                    $operation = $dom->createElement('operation'); 
                    $operation->setAttribute('option', 'S');
                    $field->appendChild($operation);                    
                    $columnName = $dom->createElement('columnName', 'PAYMENT_TYPE_CODE');
                    $field->appendChild($columnName);
                    $newContents = $dom->createElement('newContents', 'S');
                    $field->appendChild($newContents);
                    $record->appendChild($field);

                    $step->appendChild($record);
                    $transaction->appendChild($step);

                    break;
                case 8:
                    $this->line8 = true;
                    $this->batchline = $this->linecount;
                    if (Str::substr($line, 32, 12) == (string)$this->totalCredits)
                        $this->valid = false;
                    if ($this->linecount < 4)
                        $this->valid = false;
                    break;
                case 9:
                    $this->line9 = true;
                    break;
                default:
                    $this->valid = false;
                    break;
            }
        }

        $resultText = "<strong>File is Valid</strong><br/>Number of payees: ". (string)$this->payeeCount ."<br/>Total Credits: ". number_format(floatval($this->totalCredits)/100, 2);
        $resultText .= "<br/><br/>The file has been submitted for processing. Please contact us immediately if the amount or number of payees differs from what is shown above.";

        $step = $dom->createElement('step'); 

        $postingRequest = $dom->createElement('postingRequest'); 
        $targetSerial = $dom->createElement('targetSerial', '914213');
        $postingRequest->appendChild($targetSerial);
        $targetCategory = $dom->createElement('targetCategory');
        $targetCategory->setAttribute('option', 'S');
        $postingRequest->appendChild($targetCategory);
        $category = $dom->createElement('category');
        $category->setAttribute('option', 'W');
        $postingRequest->appendChild($category);
        $source = $dom->createElement('source');
        $source->setAttribute('option', 'J');
        $postingRequest->appendChild($source);
        $description = $dom->createElement('description', 'Payroll Funding - '. $dt->format("Y-m-d"));
        $postingRequest->appendChild($description);
        $amount = $dom->createElement('amount', number_format(floatval($this->totalCredits)/100, 2, '.', ''));
        $postingRequest->appendChild($amount);
        $step->appendChild($postingRequest);

        $postingRequest = $dom->createElement('postingRequest'); 
        $targetGLAccountSerial = $dom->createElement('targetGLAccountSerial', '3690');
        $postingRequest->appendChild($targetSerial);
        $targetCategory = $dom->createElement('targetGLCategory');
        $targetCategory->setAttribute('option', 'DG');
        $postingRequest->appendChild($targetCategory);
        $category = $dom->createElement('category');
        $category->setAttribute('option', 'G');
        $postingRequest->appendChild($category);
        $targetGLEntryType = $dom->createElement('targetGLEntryType');
        $targetGLEntryType->setAttribute('option', 'C');
        $postingRequest->appendChild($targetGLEntryType);
        $targetGLComment = $dom->createElement('targetGLComment', 'Payroll Funding - '. $dt->format("Y-m-d"));
        $postingRequest->appendChild($targetGLComment);
        $amount = $dom->createElement('amount', number_format(floatval($this->totalCredits)/100, 2, '.', ''));
        $postingRequest->appendChild($amount);
        $step->appendChild($postingRequest);
        $transaction->appendChild($step);

        $step = $dom->createElement('step'); 
        $feeAssess = $dom->createElement('feeAssess'); 
        $feeSerial = $dom->createElement('feeSerial', '1806');
        $feeAssess->appendChild($feeSerial);
        $targetCategory = $dom->createElement('targetCategory');
        $targetCategory->setAttribute('option', 'S');
        $feeAssess->appendChild($targetCategory);
        $targetSerial = $dom->createElement('targetSerial', '914212');
        $feeAssess->appendChild($targetSerial);
        $specifiedFeeAmount = $dom->createElement('specifiedFeeAmount', 10.00);
        $feeAssess->appendChild($specifiedFeeAmount);
        $specifiedFeeOption = $dom->createElement('specifiedFeeOption');
        $specifiedFeeOption->setAttribute('option', 'Y');
        $feeAssess->appendChild($specifiedFeeOption);
        $step->appendChild($feeAssess);
        $transaction->appendChild($step);

        $sequence->appendChild($transaction);
        $root->appendChild($sequence);
        $dom->appendChild($root); 
        $dom->formatOutput = true;        
        $xmlfileName = 'gipsonPayroll-'.$dt->format('Ymd').$dt->timestamp.'.xml';
        $dom->save(storage_path('app/public/payroll/'.$xmlfileName)); 

        $recordTitle = 'gipsonPayroll-'.$dt->format('Ymd').$dt->timestamp;

        $record = PayrollUpload::updateOrCreate(
            ['title' => $recordTitle],
            ['of_link' => $fileName, 'uf_link' => $xmlfileName]
        );

        $notify_email = app(SettingRepository::class)->byKey('notification_emails', 'notification');
        $notify_email = explode(',', $notify_email);

        $users = [];
        foreach($notify_email as $key => $ut){
            $ua = [];
            $ua['email'] = $ut;
            $ua['name'] = 'ASE Credit Union';
            $users[$key] = (object)$ua;
        }

        Mail::to($users)->send(new UploadNotification([
            'payeeCount' => $this->payeeCount,
            'totalCredits' => number_format(floatval($this->totalCredits)/100, 2, '.', ''),
            'fileLink' => Storage::url('payroll/'.$xmlfileName),
            'fileName' => $xmlfileName
        ]));
    }
}