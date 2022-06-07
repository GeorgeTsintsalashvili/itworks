<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \PHPMailer\PHPMailer\PHPMailer;
use PDF as DOMPDF;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

class InvoiceController extends Controller
{
    public function index()
    {
      return \View::make('contents.controlPanel.invoice.index');
    }

    public function display(Request $request)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $fields =['title', 'price', 'quantity', 'systemPart', 'code', 'name', 'phone', 'address', 'email', 'invoice-id', 'incrp'];

      $parameters = $request -> only($fields);

      $rules = ['title' => ['required', 'array'],
                'price' => ['required', 'array'],
                'quantity' => ['required', 'array'],
                'systemPart' => ['required', 'array'],
                'title.*' => ['required', 'string', 'min:1', 'max:300'],
                'price.*' => ['required', new NaturalNumber],
                'quantity.*' => ['required', new NaturalNumber],
                'systemPart.*' => ['required', new BinaryValue],
                'code' => ['nullable', 'string', 'max:100'],
                'name' => ['nullable', 'string', 'max:100'],
                'phone' => ['nullable', 'string', 'max:100'],
                'address' => ['nullable', 'string', 'max:200'],
                'email' => ['nullable', 'string', 'email', 'max:100'],
                'invoice-id' => ['string', 'min:1', 'max:50'],
                'incrp' => ['required', 'string', 'regex:/^\-?(0|[1-9]\d*)$/']];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $titles = $parameters['title'];
        $prices = $parameters['price'];
        $quantities = $parameters['quantity'];
        $systemParts = $parameters['systemPart'];
        $incrp = (int) $parameters['incrp'];

        $allowedSize = count($titles);
        $sizesAreTheSame = count($prices) == $allowedSize && count($quantities) == $allowedSize && count($systemParts) == $allowedSize;

        $partsPrice = 0;
        $systemPrice = 0;

        $products = [];
        $systemComponents = [];

        $clientId = null;
        $manager = null;

        if($sizesAreTheSame)
        {
          for($i = 0; $i < $allowedSize; $i++)
          {
            $price = $prices[$i] * $quantities[$i];
            $systemPart = (int) $systemParts[$i];

            $record = ['title' => $titles[$i],
                       'price' => $price,
                       'quantity' => $quantities[$i]];

            if($systemPart)
            {
              $systemPrice += $price;
              $systemComponents[] = $record;
            }

            else
            {
              $partsPrice += $price;
              $products[] = $record;
            }
          }
        }

        $systemPrice = abs($systemPrice + $incrp);

        $parameters['name'] = isset($parameters['name']) ? $parameters['name'] : null;
        $parameters['address'] = isset($parameters['address']) ? $parameters['address'] : null;
        $parameters['email'] = isset($parameters['email']) ? $parameters['email'] : null;
        $parameters['phone'] = isset($parameters['phone']) ? $parameters['phone'] : null;
        $parameters['code'] = isset($parameters['code']) ? $parameters['code'] : null;

        return \View::make('contents.controlPanel.invoice.display') -> with([
          'products' => $products,
          'systemComponents' => $systemComponents,
          'partsPrice' => $partsPrice,
          'totalPrice' => $partsPrice + $systemPrice,
          'numOfSystemComponents' => count($systemComponents),
          'systemPrice' => $systemPrice,
          'contact' => \DB::table('contacts') -> first(),
          'name' => $parameters['name'],
          'address' => $parameters['address'],
          'email' => $parameters['email'],
          'phone' => $parameters['phone'],
          'code' => $parameters['code'],
          'invoice' => $parameters['invoice-id']
        ]);
      }
    }

    public function send(Request $request)
    {
      if($request -> hasFile('invoice-document') && $request -> file('invoice-document') -> isValid())
      {
        $file = $request -> file('invoice-document');
        $email = $request -> input('email');
        $invoiceId = $request -> input('invoice-id');

        $rules = ['invoice-document' => 'bail|required|mimes:pdf|max:4096',
                  'email' => 'bail|required|string|email',
                  'invoice-id' => 'bail|required|string|min:1|max:50'];

        $formData = ['invoice-document' => $file,
                     'email' => $email,
                     'invoice-id' => $invoiceId];

        $validator = \Validator::make($formData, $rules);

        if(!$validator -> fails())
        {
          $extension = $file -> getClientOriginalExtension();
          $fileName = "ინვოისი #" . $formData['invoice-id'] . "." . $extension;

          $recipientAddress = $formData['email'];
          $subject = "ITWorks ინვოისი";
          $senderName = "ITWorks";
          $senderAddress = "info@itw.ge";
          $headers = ["MIME-Version: 1.0", "Content-Type: text/html;charset=utf-8"];

          $mailer = new PHPMailer();

          $mailer -> CharSet = "UTF-8";
          $mailer -> Host = "mail.itw.ge"; // mail.itworks.ge

          $mailer -> SMTPAuth = true;
        //  $mailer -> SMTPDebug = 3;
          $mailer -> Username = "info@itw.ge";
          $mailer -> Password = "Unicef1993$";
          $mailer -> SMTPSecure = "ssl";
          $mailer -> Port = 465;

          $mailer -> isHTML(true);
          $mailer -> FromName = $senderName;
          $mailer -> From = $senderAddress;
          $mailer -> addAddress($recipientAddress);
          $mailer -> Subject = $subject;
          $mailer -> Body = "გამარჯობათ, გიგზავნით ინვოისს.";
          $mailer -> addReplyTo($senderAddress, "Reply");
          $mailer -> AddAttachment($file -> getPathName(), $fileName);

          if($mailer -> send())
          {
              return ['success' => true];
          }
        }
      }

      return ['success' => false];
    }
}
