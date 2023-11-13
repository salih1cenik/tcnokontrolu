<!DOCTYPE html>
<html>
<head>
	<title>TC Kimlik Numarası Doğrulama</title>
</head>
<body>
	<form method="post">
		<label for="isim">İsim:</label>
		<input type="text" name="isim" id="isim" required><br><br>

		<label for="soyisim">Soyisim:</label>
		<input type="text" name="soyisim" id="soyisim" required><br><br>

		<label for="dogumyili">Doğum Yılı:</label>
		<input type="text" name="dogumyili" id="dogumyili" required><br><br>

		<label for="tcno">TC Kimlik Numarası:</label>
		<input type="text" name="tcno" id="tcno" required><br><br>

		<input type="submit" value="Doğrula">
	</form>
<style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            display: flex;
            flex-wrap: wrap;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            width: 100%;
        }

        .half-width {
            width: calc(50% - 8px);
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
	<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		function tcno_dogrula($bilgiler){
			$gonder = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				<soap:Body>
					<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
						<TCKimlikNo>'.$bilgiler["tcno"].'</TCKimlikNo>
						<Ad>'.$bilgiler["isim"].'</Ad>
						<Soyad>'.$bilgiler["soyisim"].'</Soyad>
						<DogumYili>'.$bilgiler["dogumyili"].'</DogumYili>
					</TCKimlikNoDogrula>
				</soap:Body>
			</soap:Envelope>';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $gonder);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'POST /Service/KPSPublic.asmx HTTP/1.1',
				'Host: tckimlik.nvi.gov.tr',
				'Content-Type: text/xml; charset=utf-8',
				'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
				'Content-Length: '.strlen($gonder)
			));

			$gelen = curl_exec($ch);
			curl_close($ch);

			return strip_tags($gelen);
		}

		$isim = $_POST["isim"];
		$soyisim = $_POST["soyisim"];
		$dogumyili = $_POST["dogumyili"];
		$tcno = $_POST["tcno"];


		$sonuc = tcno_dogrula(array(
			"isim" => strtoupper($isim),
			"soyisim" => strtoupper($soyisim),
			"dogumyili" => $dogumyili,
			"tcno" => $tcno
		));

		if ($sonuc == "true") {
			echo "<p style='color:green'>Doğrulama başarılı.</p>";
		} else {
			echo "<p style='color:red'>Doğrulama başarısız.</p>";
		}
	}
	?>
</body>
</html>
