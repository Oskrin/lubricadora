<?php 
	function correo($fecha, $valor, $xml, $pdf, $nombre, $correo, $dataXML, $dataPDF, $tipoEnvio ) {

		$from = 'oskr.trov@gmail.com';
		$fromName = 'COATL FAC';
		$subject = 'Notificación Electrónica'; 

		$file = $dataXML;
		$filePdf = $dataXML;

		$htmlContent =  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		      <html xmlns="http://www.w3.org/1999/xhtml">
		      <head>
		        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		        <meta name="viewport" content="width=device-width, initial-scale=1" />
		        <title>Narrative Confirm Email</title>
		        <style type="text/css">

		        /* Take care of image borders and formatting */

		        img {
		          max-width: 600px;
		          outline: none;
		          text-decoration: none;
		          -ms-interpolation-mode: bicubic;
		        }

		        a {
		          border: 0;
		          outline: none;
		        }

		        a img {
		          border: none;
		        }

		        /* General styling */

		        td, h1, h2, h3  {
		          font-family: Helvetica, Arial, sans-serif;
		          font-weight: 400;
		        }

		        td {
		          font-size: 13px;
		          line-height: 150%;
		          text-align: left;
		        }

		        body {
		          -webkit-font-smoothing:antialiased;
		          -webkit-text-size-adjust:none;
		          width: 100%;
		          height: 100%;
		          color: #37302d;
		          background: #ffffff;
		        }

		        table {
		          border-collapse: collapse !important;
		        }


		        h1, h2, h3 {
		          padding: 0;
		          margin: 0;
		          color: #444444;
		          font-weight: 400;
		          line-height: 110%;
		        }

		        h1 {
		          font-size: 35px;
		        }

		        h2 {
		          font-size: 30px;
		        }

		        h3 {
		          font-size: 24px;
		        }

		        h4 {
		          font-size: 18px;
		          font-weight: normal;
		        }

		        .important-font {
		          color: #21BEB4;
		          font-weight: bold;
		        }

		        .hide {
		          display: none !important;
		        }

		        .force-full-width {
		          width: 100% !important;
		        }

		        </style>

		        <style type="text/css" media="screen">
		            @media screen {
		              @import url(http://fonts.googleapis.com/css?family=Open+Sans:400);

		              /* Thanks Outlook 2013! */
		              td, h1, h2, h3 {
		                font-family: "Open Sans", "Helvetica Neue", Arial, sans-serif !important;
		              }
		            }
		        </style>

		        <style type="text/css" media="only screen and (max-width: 600px)">
		          /* Mobile styles */
		          @media only screen and (max-width: 600px) {

		            table[class="w320"] {
		              width: 320px !important;
		            }

		            table[class="w300"] {
		              width: 300px !important;
		            }

		            table[class="w290"] {
		              width: 290px !important;
		            }

		            td[class="w320"] {
		              width: 320px !important;
		            }

		            td[class~="mobile-padding"] {
		              padding-left: 14px !important;
		              padding-right: 14px !important;
		            }

		            td[class*="mobile-padding-left"] {
		              padding-left: 14px !important;
		            }

		            td[class*="mobile-padding-right"] {
		              padding-right: 14px !important;
		            }

		            td[class*="mobile-block"] {
		              display: block !important;
		              width: 100% !important;
		              text-align: left !important;
		              padding-left: 0 !important;
		              padding-right: 0 !important;
		              padding-bottom: 15px !important;
		            }

		            td[class*="mobile-no-padding-bottom"] {
		              padding-bottom: 0 !important;
		            }

		            td[class~="mobile-center"] {
		              text-align: center !important;
		            }

		            table[class*="mobile-center-block"] {
		              float: none !important;
		              margin: 0 auto !important;
		            }

		            *[class*="mobile-hide"] {
		              display: none !important;
		              width: 0 !important;
		              height: 0 !important;
		              line-height: 0 !important;
		              font-size: 0 !important;
		            }

		            td[class*="mobile-border"] {
		              border: 0 !important;
		            }
		          }
		        </style>
		      </head>
		      <body class="body" style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none" bgcolor="#ffffff">
		      <table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%">
		        <tr>
		          <td align="center" valign="top" bgcolor="#ffffff"  width="100%">

		          <table cellspacing="0" cellpadding="0" width="100%">
		            <tr>
		              <td style="border-bottom:1px solid #e7e7e7;">
		                <center>
		                  <table cellpadding="0" cellspacing="0" width="600" class="w320">
		                    <tr>
		                      <td align="left" class="mobile-padding" style="padding:20px; text-align:justify">
		                        <br class="mobile-hide" />
		                        <h2>COATL FAC.</h2><br>
		                        Estimado(a) '.$nombre.',<br><br>
		                        Su comprobante electrónico con fecha '.$fecha.' y cuyo valor es de $ '.$valor.' usd, ha sido generada con éxito y se encuentra disponible en nuestro portal Web.
		              Para visualizar y/o descargar la factura electrónica es necesario:
		              Ingresar a nuestro portal web facturacion.vadowservice.com/repositorio<br>                                    
		                      </td>
		                      <td class="mobile-hide" style="padding-top:20px;padding-bottom:0; vertical-align:bottom;" valign="bottom">
		                        <table cellspacing="0" cellpadding="0" width="100%">
		                          <tr>
		                            <td align="right" valign="bottom" style="padding-bottom:0; vertical-align:bottom;">
		                              <img  style="vertical-align:bottom;" src="https://www.filepicker.io/api/file/9f3sP1z8SeW1sMiDA48o"  width="174" height="294" />
		                            </td>
		                          </tr>
		                        </table>
		                      </td>
		                    </tr>
		                  </table>
		                </center>
		              </td>
		            </tr>      
		          </table>
		          </td>
		        </tr>
		      </table>
		      </body>
		      </html>';	

		$headers = "From: $fromName"." <".$from.">";

		//boundary 
		$semi_rand = md5(time()); 
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

		//headers for attachment 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

		//multipart boundary 
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
		"Content-Transfer-Encoding: 8bit\n\n" . $htmlContent . "\n\n"; 

		
		
        $message .= "--{$mime_boundary}\n";
        if($tipoEnvio == '1') {
        	$fp =    @fopen($file,"rb");
	        $data =  @fread($fp,filesize($file));
	        @fclose($fp);
	        $data = chunk_split(base64_encode($data));
	        $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
	        "Content-Description: ".basename($file)."\n" .
	        "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
	        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";	
        } else {
        	$data = chunk_split(base64_encode($dataXML));
	        $message .= "Content-Type: application/octet-stream; name=\"".basename($xml)."\"\n" . 
	        "Content-Description: ".basename($xml)."\n" .
	        "Content-Disposition: attachment;\n" . " filename=\"".basename($xml)."\"; \n" . 
	        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";		
        }
        
        $message .= "--{$mime_boundary}\n";
        $data = chunk_split(base64_encode($dataPDF));
        $message .= "Content-Type: application/pdf; name=\"".$pdf."\"\n" . 
        "Content-Description: ".$pdf."\n" .
        "Content-Disposition: attachment;\n" . " filename=\"".$pdf."\"; \n" . 
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                
		$message .= "--{$mime_boundary}--";
		$returnpath = "-f" . $from;

		//send email
		$mail = @mail($correo, $subject, $message, $headers, $returnpath); 

		//email sending status
		return $mail?"1":"0";
	}	  
?>
