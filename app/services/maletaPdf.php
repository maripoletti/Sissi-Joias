<?php
declare(strict_types=1);

// PDF simples, sem dependências: texto em Courier/WinAnsi, com quebra de páginas.
class MaletaPdf {
    public static function dinheiro(int $cents): string {
        return 'R$ ' . number_format($cents / 100, 2, ',', '.');
    }

    public static function centavos(string $price): int {
        if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/D', $price)) {
            throw new InvalidArgumentException('Preço inválido.');
        }
        [$units, $fraction] = array_pad(explode('.', $price), 2, '');
        return (int)$units * 100 + (int)str_pad($fraction, 2, '0');
    }

    public static function desconto(int $cents, int $percent): int {
        return intdiv($cents * (100 - $percent) + 50, 100);
    }

    public static function gerar(array $document, string $format = 'a4'): string {
        if (!in_array($format, ['a4', '58mm'], true)) throw new InvalidArgumentException('Formato inválido.');
        $a4 = $format === 'a4';
        $width = $a4 ? 595.28 : 164.41;
        $height = 841.89;
        $margin = $a4 ? 36 : 8;
        $font = $a4 ? 10 : 7;
        $step = $a4 ? 14 : 10;
        $columns = (int)(($width - 2 * $margin) / ($font * 0.6));
        $encode = static fn($text) => mb_convert_encoding(preg_replace('/[\x00-\x1F\x7F]/u', ' ', (string)$text), 'Windows-1252', 'UTF-8');
        $header = [];
        foreach (['SISSI SEMIJOIAS E ACESSÓRIOS', 'Maleta: ' . $document['nome'],
            'Revendedora: ' . $document['revendedora'], 'Envio: ' . $document['data'], str_repeat('-', $columns)] as $line) {
            array_push($header, ...explode("\n", wordwrap($encode($line), $columns, "\n", true)));
        }
        $lines = [];
        $total = 0;
        $originalTotal = 0;
        foreach ($document['itens'] as $item) {
            $quantity = (int)$item['quantidade'];
            $original = self::centavos((string)$item['original']);
            $final = self::centavos((string)$item['final']);
            $total += $quantity * $final;
            $originalTotal += $quantity * $original;
            $texts = [$item['produto']];
            if ((int)$item['desconto'] > 0) {
                $texts[] = 'Original: ' . self::dinheiro($original) . ' | Cupom: ' . $item['desconto'] . '%';
            }
            $texts[] = $quantity . ' x ' . self::dinheiro($final) . ' = ' . self::dinheiro($quantity * $final);
            $texts[] = '';
            foreach ($texts as $text) array_push($lines, ...explode("\n", wordwrap($encode($text), $columns, "\n", true)));
        }
        foreach ([str_repeat('-', $columns), 'Valor original: ' . self::dinheiro($originalTotal),
            'Descontos: ' . self::dinheiro($originalTotal - $total), 'TOTAL: ' . self::dinheiro($total)] as $text) {
            array_push($lines, ...explode("\n", wordwrap($encode($text), $columns, "\n", true)));
        }
        $capacity = max(1, (int)(($height - 2 * $margin) / $step) - count($header) - 3);
        $pages = array_chunk($lines, $capacity);
        $objects = [1 => '<< /Type /Catalog /Pages 2 0 R >>', 2 => '',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>'];
        $kids = [];
        foreach ($pages as $index => $body) {
            $pageId = 4 + $index * 2;
            $contentId = $pageId + 1;
            $kids[] = "$pageId 0 R";
            $pageLines = array_merge($header, $body, ['', 'Pagina ' . ($index + 1) . '/' . count($pages)]);
            // O papel de 58 mm usa a altura necessária para cada trecho do comprovante.
            $pageHeight = $a4 ? $height : count($pageLines) * $step + 2 * $margin;
            $stream = "BT /F1 $font Tf $step TL $margin " . ($pageHeight - $margin - $font) . " Td\n";
            foreach ($pageLines as $line) {
                $literal = strtr($line, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
                $stream .= "($literal) Tj T*\n";
            }
            $stream .= 'ET';
            $objects[$pageId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $width $pageHeight] /Resources << /Font << /F1 3 0 R >> >> /Contents $contentId 0 R >>";
            $objects[$contentId] = '<< /Length ' . strlen($stream) . ">>\nstream\n$stream\nendstream";
        }
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($pages) . ' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$object\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref' . "\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($objects as $id => $_) $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        return $pdf . 'trailer << /Size ' . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";
    }
}
