<?php

namespace App\Bll;

use Exception;
use Illuminate\Support\Facades\Config;
use SendGrid\Mail\Mail;

class SendGrid
{
	// private const  API_KEY = 't_qHBS2wlLs4uZHPKJF3OA';
	// private const  URL = 'https://mandrillapp.com/api/1.0/messages/send';
	// private const SUBACCOUNT = "sallatk_sender";

	// private $error = "";
	// private $from = "register@sallatk.com";
	// private  $from_name;
	// private $receipients = [];

	private static function sendMessage($m)
	{
		$token = Config::get('mail.mail-token');
		$sendgrid = new \SendGrid($token);
		try {
			$response = $sendgrid->send($m);
			// dd($response);
			// print $response->statusCode() . "\n";
			// print_r($response->headers());
			// print $response->body() . "\n";
		} catch (Exception $e) {
			echo 'Caught exception: ' . $e->getMessage() . "\n";
		}
	}

	private static function getFrom()
	{
		$from = [
			'email' => 'no-reply@sallatk.com',
			'name' => 'Sallatk',
		];

		$settings = get_main_settings();

		$store = get_store();

		if ($settings != null && $store->domain_status == 1) {
			if($store->domain == null)
			{
				$from['email'] = 'no-reply@sallatk.com';
				$from['name'] = $settings->name ?? $from['name'];

			}
			else
			{
				$from['email'] = 'no-reply@' . $store->domain . '.' . $store->domain_extension;
				$from['name'] = $settings->name ?? $from['name'];
			}
		}

		return $from;
	}

	public static function send($to, $email, $type = null, $pdf = null, $order = null, $from = null)
	{
		$settings = get_settings();

		if ($from == null) {
			$from = self::getFrom();
			// $from = 'no-reply@sallatk.com';
		} else {
			$from = [
				'email' => $from,
				'name' => 'Sallatk',
			];
		}

		if ($settings != null && $settings->email != "") {
			$get_type  = gettype($email);
			$encoded_file = chunk_split(base64_encode($pdf));
			if ($type == "pdf") {
				$html = $email->build()->pdf;
				// $m = new MailChimp($settings->email);
				// $m->AddTo($to, $to);
				// $m->sendpdf('sallatk', $html, $encoded_file);
				$m = new Mail();
				$m->setFrom($from['email'], $from['name']);
				$m->setSubject($email->subject);
				$m->addTo($to, $to);
				$m->addContent("text/html", $html);
				$m->addAttachment($encoded_file, "application/pdf", "attachment.pdf", "attachment");

				self::sendMessage($m);
			} else {
				$html = $email->build()->html;
				// $m = new MailChimp($settings->email);
				// $m->AddTo($to, $to);
				// $m->send($email->subject, $html);
				$m = new Mail();
				$m->setFrom($from['email'], $from['name']);
				$m->setSubject($email->subject);
				$m->addTo($to, $to);
				$m->addContent("text/html", $html);

				self::sendMessage($m);
			}
		} else {
			$get_type  = gettype($email);
			$encoded_file = chunk_split(base64_encode($pdf));
			if ($type == "pdf") {
				$html = $email->build()->pdf;
				// $m = new MailChimp();
				// $m->AddTo($to, $to);
				// $m->sendpdf('sallatk', $html, $encoded_file);
				// $get_error =  $m->GetError();
				$m = new Mail();
				$m->setFrom($from['email'], $from['name']);
				$m->setSubject($email->subject);
				$m->addTo($to, $to);
				$m->addContent("text/html", $html);
				$m->addAttachment($encoded_file, "application/pdf", "attachment.pdf", "attachment");

				self::sendMessage($m);
			} else {
				$html = $email->build()->html;
				// $m = new MailChimp();
				// $m->AddTo($to, $to);
				// $m->send($email->subject, $html);
				$m = new Mail();
				$m->setFrom($from['email'], $from['name']);
				$m->setSubject($email->subject);
				$m->addTo($to, $to);
				$m->addContent("text/html", $html);

				self::sendMessage($m);
			}
		}
	} // end of Method Send

	public static function sendDate($user, $store)
	{
		$settings = get_settings();
		$date = date("d-m-Y", strtotime($store->package_ends_at));
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();

		if ($settings != '') {

			// $m = new MailChimp($settings->email);
			// $m->AddTo($user[0]->name, $user[0]->email);
			// $m->send($user[0]->name, "سيتم انتهاء الباقه المحدد !  {$date}   لذا يجب تجديد الباقه قبل الانتهاء");
			$m = new Mail();
			$m->setFrom($from['email'], $from['name']);
			$m->setSubject("Sallatk");
			$m->addTo($user[0]->name, $user[0]->email);
			$m->addContent("text/html", "سيتم انتهاء الباقه المحدد !  {$date}   لذا يجب تجديد الباقه قبل الانتهاء");

			self::sendMessage($m);
		} else {

			$m = new Mail();
			$m->setFrom($from['email'], $from['name']);
			$m->setSubject("Sallatk");
			$m->addTo($user[0]->name, $user[0]->email);
			$m->addContent("text/html", "سيتم انتهاء الباقه المحدد !  {$date}   لذا يجب تجديد الباقه قبل الانتهاء");

			self::sendMessage($m);
		}
	} // end of Method sendDate

	public static function sendReplay($email, $message)
	{
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();
		// $m = new MailChimp();
		// $m->AddTo($email, $email);
		// $m->send('Reply Message', $message);
		$m = new Mail();
		$m->setFrom($from['email'], $from['name']);
		$m->setSubject("Sallatk Reply Message");
		$m->addTo($email, $email);
		$m->addContent("text/html", $message);

		self::sendMessage($m);
	} //End Of Send Reply

	public static function sendContact($email, $message)
	{
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();
		// $m = new MailChimp();
		// $m->AddTo($email, $email);
		// $m->send('Reply Message', $message);
		$m = new Mail();
		$m->setFrom($from['email'], $from['name']);
		$m->setSubject("Sallatk Contact Us Message");
		$m->addTo($email, $email);
		$m->addContent("text/html", $message);

		self::sendMessage($m);
	} //End Of sendContact Reply

	public static function sendReport($email, $email_store, $body)
	{
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();
		if ($email_store != '') {
			// $m = new MailChimp($email_store);
			// $m->AddTo($email, $email);
			// $m->send('Reply Message Report', $body);
			$m = new Mail();
			$m->setFrom($from['email'], $from['name']);
			$m->setSubject("Reply Message Report");
			$m->addTo($email, $email);
			$m->addContent("text/html", $body);

			self::sendMessage($m);
		} else {
			// $m = new MailChimp();
			// $m->AddTo($email, $email);
			// $m->send('Reply Message Report', $body);
			$m = new Mail();
			$m->setFrom($from['email'], $from['name']);
			$m->setSubject("Reply Message Report");
			$m->addTo($email, $email);
			$m->addContent("text/html", $body);

			self::sendMessage($m);
		}
	} //End Of Send Reply

	public static function sendEmail($email, $msg, $email_store, $subject = null)
	{
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();
		if ($subject == null) {
			$subject = 'Message From Sallatk';
		}
		// $m = new MailChimp($email_store);
		// $m->AddTo($email, $email);
		// $m->send($subject, $msg);

		$m = new Mail();
		$m->setFrom($from['email'], $from['name']);
		$m->setSubject($subject);
		$m->addTo($email, $email);
		$m->addContent("text/html", $msg);

		self::sendMessage($m);
	} //End Of Send Order Message

	public static function sendEmailSections($name, $to_email, $email_store)
	{
		// $from = Config::get('mail.mail-from');
		$from = self::getFrom();
		// $m = new MailChimp($email_store);
		// $m->AddTo($name, $to_email);
		// $m->send('Section Message', "تم اضافة قسم جديد بواسطه {$name}");
		$m = new Mail();
		$m->setFrom($from['email'], $from['name']);
		$m->setSubject("Reply Message Report");
		$m->addTo($to_email, $name);
		$m->addContent("text/html", "تم اضافة قسم جديد بواسطه {$name}");

		self::sendMessage($m);
	} //End Of Send Section Message

	public static function createSenderIdentity($domain)
	{
		$token = Config::get('mail.mail-token');
		$sg = new \SendGrid($token);

		$email = 'no-reply@' . strtolower($domain);

		$name = $domain;

		$request_body = json_decode('{
			"nickname": "' . $domain . '",
			"from_email": "' . $email . '",
			"from_name": "' . $name . '",
			"reply_to": "' . $email . '",
			"reply_to_name": "' . $name . '",
			"address": "1234 Fake St",
			"address2": "PO Box 1234",
			"state": "CA",
			"city": "San Francisco",
			"country": "USA",
			"zip": "94105"
		}');
		try {
			$response = $sg->client->verified_senders()->post($request_body);
// 			print $response->statusCode() . "\n";
// 			print_r($response->headers());
// 			print $response->body() . "\n";

			self::sendToSupport($domain, $email);
		} catch (Exception $ex) {
			echo 'Caught exception: ' .  $ex->getMessage();
		}
	}

	private static function sendToSupport($domain, $email)
	{
		$from = 'no-reply@sallatk.com';
		$to = 'ahmed@serv5.com';
		$cc = 'karim@serv5.com';
		$m = new Mail();
		$m->setFrom($from, "Sallatk");
		$m->setSubject("New Domain & Email Registered");
		$m->addTo($to, $to);
		$m->addCc($cc, $cc);
		$m->addContent("text/html", "New Domain Registered {$domain} Requires Email Activation on SendGrid for ${email}");

		self::sendMessage($m);
	}
}
