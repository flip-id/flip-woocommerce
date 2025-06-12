<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FlipForBusiness_Utils {

   /**
    * Helper for backward compatibility WC v3 & v2 on getting Order Property
    * @param  [String] $order Order Object
    * @param  [String] $property Target property
   */
   public static function getOrderProperty($order, $property){
      $functionName = "get_".$property;
      if (method_exists($order, $functionName)){ // WC v3
         return (string)$order->{$functionName}();
      } else { // WC v2
         return (string)$order->{$property};
      }
   }

   /**
    * Helper Calc time
    * @param mixed $duration
    * @return string
    * */
   public static function calculateTime($time = '15 minutes') {
      $currentTime = new DateTime(current_time('Y-m-d H:i'));
      $currentTime->modify($time);

      return $currentTime->format('Y-m-d H:i');
   }

   /**
    * Add prefix to order ID for Flip API on Flip bill title
    * @param string $order_id
    * @return string
    */
   public static function addFlipOrderPrefix($order_id) {
      $options = get_option('woocommerce_flip_settings');
      $prefix = !empty($options['order_id_prefix']) ? $options['order_id_prefix'] : 'FWOrder-';
      
      // Sanitize prefix to only allow alphanumeric characters, hyphens, and underscores
      $prefix = preg_replace('/[^a-zA-Z0-9_\-]/', '', $prefix);
      
      // Ensure we have a valid prefix
      if (empty($prefix)) {
         $prefix = 'FWOrder-';
      }
      
      return $prefix . $order_id;
   }

   /**
    * Remove prefix from bill title returned by Flip API
    * @param string $bill_title
    * @return string
    */
   public static function removeFlipOrderPrefix($bill_title) {
      $options = get_option('woocommerce_flip_settings');
      $prefix = !empty($options['order_id_prefix']) ? $options['order_id_prefix'] : 'FWOrder-';
      
      // Sanitize prefix to only allow alphanumeric characters, hyphens, and underscores
      $prefix = preg_replace('/[^a-zA-Z0-9_\-]/', '', $prefix);
      
      // Ensure we have a valid prefix
      if (empty($prefix)) {
         $prefix = 'FWOrder-';
      }
      
      if (strpos($bill_title, $prefix) === 0) {
         return substr($bill_title, strlen($prefix));
      }
      return $bill_title;
   }

   /**
    * Data Destination Bank
    */
   public static function flip_destination_bank_lists() {
      $banks = array(
         "harda" => "Allo Bank/Bank Harda Internasional",
         "anz" => "ANZ Indonesia",
         "aceh" => "Bank Aceh Syariah",
         "aladin" => "Bank Aladin Syariah",
         "amar" => "Bank Amar Indonesia",
         "antardaerah" => "Bank Antardaerah",
         "artha" => "Bank Artha Graha Internasional",
         "bengkulu" => "Bank Bengkulu",
         "daerah_istimewa" => "Bank BPD DIY",
         "daerah_istimewa_syr" => "Bank BPD DIY Syariah",
         "btpn_syr" => "Bank BTPN Syariah",
         "bukopin_syr" => "Bank Bukopin Syariah",
         "bumi_arta" => "Bank Bumi Arta",
         "capital" => "Bank Capital Indonesia",
         "bca" => "Bank Central Asia",
         "ccb" => "Bank China Construction Bank Indonesia",
         "cnb" => "Bank CNB (Centratama Nasional Bank)",
         "danamon" => "Bank Danamon & Danamon Syariah",
         "dinar" => "Bank Dinar Indonesia",
         "dki" => "Bank DKI",
         "dki_syr" => "Bank DKI Syariah",
         "ganesha" => "Bank Ganesha",
         "agris" => "Bank IBK Indonesia",
         "ina_perdana" => "Bank Ina Perdana",
         "index_selindo" => "Bank Index Selindo",
         "artos_syr" => "Bank Jago Syariah",
         "jambi" => "Bank Jambi",
         "jambi_syr" => "Bank Jambi Syariah",
         "jasa_jakarta" => "Bank Jasa Jakarta",
         "jawa_tengah" => "Bank Jateng",
         "jawa_tengah_syr" => "Bank Jateng Syariah",
         "jawa_timur" => "Bank Jatim",
         "jawa_timur_syr" => "Bank Jatim Syariah",
         "kalimantan_barat" => "Bank Kalbar",
         "kalimantan_barat_syr" => "Bank Kalbar Syariah",
         "kalimantan_selatan" => "Bank Kalsel",
         "kalimantan_selatan_syr" => "Bank Kalsel Syariah",
         "kalimantan_tengah" => "Bank Kalteng",
         "kalimantan_timur_syr" => "Bank Kaltim Syariah",
         "kalimantan_timur" => "Bank Kaltimtara",
         "lampung" => "Bank Lampung",
         "maluku" => "Bank Maluku",
         "mandiri" => "Bank Mandiri",
         "mantap" => "Bank MANTAP (Mandiri Taspen)",
         "maspion" => "Bank Maspion Indonesia",
         "mayapada" => "Bank Mayapada",
         "mayora" => "Bank Mayora Indonesia",
         "mega" => "Bank Mega",
         "mega_syr" => "Bank Mega Syariah",
         "mestika_dharma" => "Bank Mestika Dharma",
         "mizuho" => "Bank Mizuho Indonesia",
         "mas" => "Bank Multi Arta Sentosa (Bank MAS)",
         "mutiara" => "Bank Mutiara",
         "sumatera_barat" => "Bank Nagari",
         "sumatera_barat_syr" => "Bank Nagari Syariah",
         "nusa_tenggara_barat" => "Bank NTB Syariah",
         "nusa_tenggara_timur" => "Bank NTT",
         "nusantara_parahyangan" => "Bank Nusantara Parahyangan",
         "ocbc" => "Bank OCBC NISP",
         "ocbc_syr" => "Bank OCBC NISP Syariah",
         "america_na" => "Bank of America NA",
         "boc" => "Bank of China (Hong Kong) Limited",
         "india" => "Bank of India Indonesia",
         "tokyo" => "Bank of Tokyo Mitsubishi UFJ",
         "papua" => "Bank Papua",
         "prima" => "Bank Prima Master",
         "bri" => "Bank Rakyat Indonesia",
         "riau_dan_kepri" => "Bank Riau Kepri",
         "sahabat_sampoerna" => "Bank Sahabat Sampoerna",
         "shinhan" => "Bank Shinhan Indonesia",
         "sinarmas" => "Bank Sinarmas",
         "sinarmas_syr" => "Bank Sinarmas Syariah",
         "sulselbar" => "Bank Sulselbar",
         "sulselbar_syr" => "Bank Sulselbar Syariah",
         "sulawesi" => "Bank Sulteng",
         "sulawesi_tenggara" => "Bank Sultra",
         "sulut" => "Bank SulutGo",
         "sumsel_dan_babel" => "Bank Sumsel Babel",
         "sumsel_dan_babel_syr" => "Bank Sumsel Babel Syariah",
         "sumut" => "Bank Sumut",
         "sumut_syr" => "Bank Sumut Syariah",
         "resona_perdania" => "Bank Resona Perdania",
         "victoria_internasional" => "Bank Victoria International",
         "victoria_syr" => "Bank Victoria Syariah",
         "woori" => "Bank Woori Saudara",
         "bca_syr" => "BCA (Bank Central Asia) Syariah",
         "bjb" => "BJB",
         "bjb_syr" => "BJB Syariah",
         "royal" => "Blu/BCA Digital",
         "bni" => "BNI (Bank Negara Indonesia)",
         "bnp_paribas" => "BNP Paribas Indonesia",
         "bali" => "BPD Bali",
         "banten" => "BPD Banten",
         "eka" => "BPR EKA (Bank Eka)",
         "agroniaga" => "BRI Agroniaga",
         "bsm" => "BSI (Bank Syariah Indonesia)",
         "btn" => "BTN",
         "btn_syr" => "BTN Syariah",
         "tabungan_pensiunan_nasional" => "BTPN",
         "cimb" => "CIMB Niaga & CIMB Niaga Syariah",
         "citibank" => "Citibank",
         "commonwealth" => "Commonwealth Bank",
         "chinatrust" => "CTBC (Chinatrust) Indonesia",
         "dbs" => "DBS Indonesia",
         "hsbc" => "HSBC Indonesia",
         "icbc" => "ICBC Indonesia",
         "artos" => "Jago/Artos",
         "hana" => "LINE Bank/KEB Hana",
         "bii" => "Maybank Indonesia",
         "bii_syr" => "Maybank Syariah",
         "mnc_internasional" => "Motion/MNC Bank",
         "muamalat" => "Muamalat",
         "yudha_bakti" => "Neo Commerce/Yudha Bhakti",
         "nationalnobu" => "Nobu (Nationalnobu) Bank",
         "panin" => "Panin Bank",
         "panin_syr" => "Panin Dubai Syariah",
         "permata" => "Permata",
         "permata_syr" => "Permata Syariah",
         "qnb_kesawan" => "QNB Indonesia",
         "rabobank" => "Rabobank International Indonesia",
         "sbi_indonesia" => "SBI Indonesia",
         "kesejahteraan_ekonomi" => "Seabank/Bank BKE",
         "standard_chartered" => "Standard Chartered Bank",
         "super_bank" => "Superbank",
         "uob" => "TMRW/UOB",
         "bukopin" => "Wokee/Bukopin",
         "dana" => "Dana",
         "gopay" => "GoPay",
         "linkaja" => "LinkAja",
         "ovo" => "OVO",
         "shopeepay" => "ShopeePay"
      );

      return $banks;
   }

   /**
    * Helper to delete all characters that are not letters, numbers, or spaces.
    */
   public static function flip_clean_string($string) {
      // Removes all non-alphabetic and non-numeric characters, except spaces.
      $cleaned_string = preg_replace('/[^A-Za-z0-9 ]/', ' ', $string);
      // Replacing underscores (_) with spaces
      $cleaned_string = str_replace('_', ' ', $cleaned_string);
      // Remove excess spaces (if any)
      $cleaned_string = trim(preg_replace('/\s+/', ' ', $cleaned_string));

      return $cleaned_string;
   }

   /**
    * Handle check if order is expired
    * @param DateTime|string $flip_expired
    * @return bool
    */
   public static function flip_is_expired($flip_expired) {
      $status = false;
      $date_now = current_time('Y-m-d H:i');

      // Ubah waktu menjadi timestamp
      $timestamp_flip_expired = strtotime($flip_expired);
      $timestamp_date_now = strtotime($date_now);
      
      if ($timestamp_flip_expired < $timestamp_date_now) {
         $status = true;
      }

      return $status;
   }
}