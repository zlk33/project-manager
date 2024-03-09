<?php
    function skrocTekst($tekst, $dlugosc = 20, $wielokropek = '...') {
        if (strlen($tekst) <= $dlugosc) {
            return $tekst;
        } else {
            $skroconyTekst = substr($tekst, 0, $dlugosc - strlen($wielokropek)) . $wielokropek;
            return $skroconyTekst;
        }
    }

    function rodzajPliku($sciezka) {
        $informacje = pathinfo($sciezka);
        $rozszerzenie = strtolower($informacje['extension']);
        switch ($rozszerzenie) {
            case 'txt':
                return '<i class="fa-regular fa-file-lines file-icon"></i>';
            case 'jpg':
            case 'jpeg':
            case 'png':
                return '<i class="fa-regular fa-file-image file-icon"></i>';
            case 'pdf':
                return '<i class="fa-regular fa-file-pdf file-icon"></i>';
            case 'doc':
            case 'docx':
                return '<i class="fa-regular fa-file-word file-icon"></i>';
            case 'xls':
            case 'xlsx':
                return '<i class="fa-regular fa-file-excel file-icon"></i>';
            case 'pptx':
            case 'ppt':
                return '<i class="fa-regular fa-file-powerpoint file-icon"></i>';
            case 'csv':
                return '<i class="fa-solid fa-file-csv file-icon"></i>';
            case 'mp3':
            case 'wav':
                return '<i class="fa-regular fa-file-audio file-icon"></i>';
            case 'zip':
            case 'rar':
            case '7z':
                return '<i class="fa-regular fa-file-zipper file-icon"></i>';
            case 'mp4':
            case 'avi':
            case 'mkv':
            case 'mov':
                return '<i class="fa-regular fa-file-video file-icon"></i>';
            default:
                return '<i class="fa-regular fa-file file-icon"></i>';
        }
    }
    function wygenerujKod() {
        $znaki = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $kod = '';
        $max = strlen($znaki) - 1;
        
        for ($i = 0; $i < 6; $i++) {
            $kod .= $znaki[mt_rand(0, $max)];
        }
        
        return $kod;
    }

?>