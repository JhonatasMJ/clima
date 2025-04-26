<?php

function obter_lat_lon_opencage($cidade) {
    $apiKey = "0dd3206b81274f6baf4d188479df56c8"; 
    $geocodeUrl = "https://api.opencagedata.com/geocode/v1/json?q=" . urlencode($cidade) . "&key=" . $apiKey;
    $ch = curl_init($geocodeUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $geoData = json_decode($response, true);

    if (isset($geoData['results'][0]['geometry']['lat']) && isset($geoData['results'][0]['geometry']['lng'])) {
        return [
            'latitude' => $geoData['results'][0]['geometry']['lat'],
            'longitude' => $geoData['results'][0]['geometry']['lng']
        ];
    } else {
        return null;
    }
}

function obter_clima_openweathermap($latitude, $longitude) {
    $apiKey = "4bf921b912462cc65e6e2c0d112f66b0"; 
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid=" . $apiKey . "&units=metric&lang=pt_br";

    $ch = curl_init($weatherUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function obter_icone_clima($codigo_icone) {
    $icones = [
        "01d" => "fas fa-sun",
        "01n" => "fas fa-moon",
        "02d" => "fas fa-cloud-sun",
        "02n" => "fas fa-cloud-moon",
        "03d" => "fas fa-cloud",
        "03n" => "fas fa-cloud",
        "04d" => "fas fa-cloud",
        "04n" => "fas fa-cloud",
        "09d" => "fas fa-cloud-showers-heavy",
        "09n" => "fas fa-cloud-showers-heavy",
        "10d" => "fas fa-cloud-rain",
        "10n" => "fas fa-cloud-rain",
        "11d" => "fas fa-bolt",
        "11n" => "fas fa-bolt",
        "13d" => "fas fa-snowflake",
        "13n" => "fas fa-snowflake",
        "50d" => "fas fa-smog",
        "50n" => "fas fa-smog",
        "default" => "fas fa-cloud"
    ];

    return isset($icones[$codigo_icone]) ? $icones[$codigo_icone] : $icones["default"];
}

function direcao_vento($graus) {
    $direcoes = [
        'N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
        'S', 'SSO', 'SO', 'OSO', 'O', 'ONO', 'NO', 'NNO', 'N'
    ];
    return $direcoes[round($graus / 22.5)];
}

$cidade = isset($_GET['cidade']) ? trim($_GET['cidade']) : '';
$mostrar_resultado = false;
$mostrar_erro = false;

if (!empty($cidade)) {
    $coords = obter_lat_lon_opencage($cidade);

    if ($coords) {
        $latitude = $coords['latitude'];
        $longitude = $coords['longitude'];
        $clima = obter_clima_openweathermap($latitude, $longitude);

        if (isset($clima['main'])) {
            $temperatura = round($clima['main']['temp']);
            $sensacao_termica = round($clima['main']['feels_like']);
            $umidade = $clima['main']['humidity'];
            $pressao = $clima['main']['pressure'];
            $velocidade_vento = $clima['wind']['speed'];
            $direcao_vento = $clima['wind']['deg'];
            $descricao_clima = ucfirst($clima['weather'][0]['description']);
            $icone_codigo = $clima['weather'][0]['icon'];
            $icone_clima = obter_icone_clima($icone_codigo);
            $hora_dado = date("H:i", $clima['dt']);
            $nascer_sol = date("H:i", $clima['sys']['sunrise']);
            $por_sol = date("H:i", $clima['sys']['sunset']);
            $nome_cidade = $clima['name'];
            $pais = $clima['sys']['country'];
            $mostrar_resultado = true;
        } else {
            $erro = "Não foi possível obter os dados do clima. Tente novamente mais tarde.";
            $mostrar_erro = true;
        }
    } else {
        $erro = "Não conseguimos encontrar a cidade. Verifique o nome e tente novamente.";
        $mostrar_erro = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaTempo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f2f5f9;
            color: #333;
            line-height: 1.4;
            padding: 15px;
        }

        .conteudo {
            max-width: 500px;
            margin: 0 auto;
        }

        .cabecalho {
            text-align: center;
            margin-bottom: 20px;
            padding-top: 15px;
        }

        .logo {
            font-size: 24px;
            font-weight: 600;
            color: #3a7bd5;
        }

        .formulario {
            display: flex;
            margin-bottom: 20px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .campo {
            flex: 1;
            padding: 10px 15px;
            border: none;
            font-size: 15px;
            outline: none;
        }

        .botao {
            background-color: #3a7bd5;
            color: white;
            border: none;
            padding: 0 15px;
            cursor: pointer;
        }

        .botao:hover {
            background-color: #2a6ac1;
        }

        .erro {
            background-color: #ffe6e6;
            color: #d32f2f;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
        }

        .cartao {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .topo {
            background-color: #3a7bd5;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .local {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .data {
            font-size: 13px;
            opacity: 0.9;
        }

        .principal {
            padding: 20px 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .icone {
            font-size: 40px;
            color: #3a7bd5;
            margin-bottom: 10px;
        }

        .temp {
            font-size: 42px;
            font-weight: 600;
            line-height: 1;
            margin-bottom: 8px;
            position: relative;
        }

        .temp::after {
            content: "°C";
            position: absolute;
            top: 5px;
            font-size: 18px;
            font-weight: 400;
        }

        .descricao {
            font-size: 16px;
            color: #666;
        }

        .detalhes {
            padding: 15px;
        }

        .grade {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .item-icone {
            color: #3a7bd5;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .item-texto {
            flex: 1;
        }

        .rotulo {
            font-size: 12px;
            color: #666;
        }

        .valor {
            font-weight: 500;
            font-size: 14px;
        }

        .sol {
            display: flex;
            justify-content: space-between;
            background-color: #f0f5ff;
            padding: 12px;
            border-radius: 6px;
        }

        .sol-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sol-icone {
            color: #f59e0b;
            font-size: 16px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="conteudo">
        <header class="cabecalho">
            <div class="logo">
                <i class="fas fa-cloud-sun"></i> ClimaTempo
            </div>
        </header>

        <form class="formulario" method="GET" action="">
            <input type="text" name="cidade" class="campo" placeholder="Digite o nome da cidade" value="<?php echo htmlspecialchars($cidade); ?>" required>
            <button type="submit" class="botao"><i class="fas fa-search"></i></button>
        </form>

        <?php if ($mostrar_erro): ?>
        <div class="erro">
            <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
        </div>
        <?php endif; ?>

        <?php if ($mostrar_resultado): ?>
        <div class="cartao">
            <div class="topo">
                <div class="local"><?php echo htmlspecialchars($nome_cidade); ?>, <?php echo $pais; ?></div>
                <div class="data">Atualizado às <?php echo $hora_dado; ?></div>
            </div>
            
            <div class="principal">
                <div class="icone">
                    <i class="<?php echo $icone_clima; ?>"></i>
                </div>
                <div class="temp"><?php echo $temperatura; ?></div>
                <div class="descricao"><?php echo $descricao_clima; ?></div>
            </div>
            
            <div class="detalhes">
                <div class="grade">
                    <div class="item">
                        <div class="item-icone"><i class="fas fa-temperature-high"></i></div>
                        <div class="item-texto">
                            <div class="rotulo">Sensação</div>
                            <div class="valor"><?php echo $sensacao_termica; ?>°C</div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item-icone"><i class="fas fa-tint"></i></div>
                        <div class="item-texto">
                            <div class="rotulo">Umidade</div>
                            <div class="valor"><?php echo $umidade; ?>%</div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item-icone"><i class="fas fa-wind"></i></div>
                        <div class="item-texto">
                            <div class="rotulo">Vento</div>
                            <div class="valor"><?php echo $velocidade_vento; ?> m/s <?php echo direcao_vento($direcao_vento); ?></div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="item-icone"><i class="fas fa-compress-arrows-alt"></i></div>
                        <div class="item-texto">
                            <div class="rotulo">Pressão</div>
                            <div class="valor"><?php echo $pressao; ?> hPa</div>
                        </div>
                    </div>
                </div>
                
                <div class="sol">
                    <div class="sol-item">
                        <div class="sol-icone"><i class="fas fa-sunrise"></i></div>
                        <div class="rotulo">Nascer</div>
                        <div class="valor"><?php echo $nascer_sol; ?></div>
                    </div>
                    <div class="sol-item">
                        <div class="sol-icone"><i class="fas fa-sunset"></i></div>
                        <div class="rotulo">Pôr do Sol</div>
                        <div class="valor"><?php echo $por_sol; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>