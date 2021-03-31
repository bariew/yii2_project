<?php
/**
 * Cbr class file
 */

namespace app\modules\nalog\components;

/**
 * Class Cbr
 * @package app\modules\nalog\components
 */
class Cbr
{
    private static $instance;
    /** @var \SoapClient  */
    private $client;

    /**
     * Cbr constructor.
     * @param \SoapClient $client
     */
    public function __construct(\SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return Cbr
     */
    public static function instance()
    {
        return static::$instance ?? new self(new \SoapClient('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL'));
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed
     * @link https://www.cbr.ru/development/DWS/
     */
    private function call($method, $params = [])
    {
//        print_r(Cbr::instance()->GetCursDynamic('2021-03-02', '2021-03-23', 'R01235'));exit;
        $result = $this->client->$method($params);
        $name = $method."Result";
        return isset($result->$name->any)
            ? json_decode(json_encode(simplexml_load_string($result->$name->any)), true)
            : $result->$name;
    }

    /** @param string $On_date 2021-03-24T00:00:00 | 2021-03-24 */
    public function GetCursOnDate($On_date){ return $this->call(static::methodName(), get_defined_vars()); }
    public function EnumValutes(bool $Seld){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateTime(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateTimeSeld(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDate(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateSeld(){ return $this->call(static::methodName(), get_defined_vars()); }
    /** @param string $ValutaCode R01235 */
    public function GetCursDynamic($FromDate, $ToDate, $ValutaCode){ return $this->call(static::methodName(), get_defined_vars()); }
    public function KeyRate($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function DragMetDynamic($fromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function NewsInfo($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function SwapDynamic($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function DepoDynamic($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function OstatDynamic($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function OstatDepo($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function mrrf($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function mrrf7D($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Saldo($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Ruonia($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function ROISfix($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function MKR($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function DV($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Repo_debt($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Coins_base($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function FixingBase($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Overnight($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function Bauction($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function SwapDayTotal($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function SwapMonthTotal($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function SwapInfoSellUSD($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function SwapInfoSellUSDVol($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function BiCurBase($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function BiCurBacket(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function RepoDebtUSD($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }

    private static function methodName()
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'];
    }

    public static function dragMets()
    {
        return [
            1 => 'gold', 2 => 'silver', 3 => 'platinum', 4 => 'palladium'
        ];
    }

    public static function valutes()
    {
        return [
            ['Vcode' => 'R01010 ', 'Vname' => 'Австралийский доллар ', 'VEngname' => 'Australian Dollar ', 'Vnom' => '1', 'VcommonCode' => 'R01010 ', 'VnumCode' => '36', 'VcharCode' => 'AUD'],
            ['Vcode' => 'R01015 ', 'Vname' => 'Австрийский шиллинг ', 'VEngname' => 'Austrian Shilling ', 'Vnom' => '1000', 'VcommonCode' => 'R01015 ', 'VnumCode' => '40', 'VcharCode' => 'ATS'],
            ['Vcode' => 'R01020A ', 'Vname' => 'Азербайджанский манат ', 'VEngname' => 'Azerbaijan Manat ', 'Vnom' => '1', 'VcommonCode' => 'R01020 ', 'VnumCode' => '944', 'VcharCode' => 'AZN'],
            ['Vcode' => 'R01035 ', 'Vname' => 'Фунт стерлингов Соединенного королевства ', 'VEngname' => 'British Pound Sterling ', 'Vnom' => '1', 'VcommonCode' => 'R01035 ', 'VnumCode' => '826', 'VcharCode' => 'GBP'],
            ['Vcode' => 'R01040F ', 'Vname' => 'Ангольская новая кванза ', 'VEngname' => 'Angolan new Kwanza ', 'Vnom' => '100000', 'VcommonCode' => 'R01040 ', 'VnumCode' => '24', 'VcharCode' => 'AON'],
            ['Vcode' => 'R01060 ', 'Vname' => 'Армянский драм ', 'VEngname' => 'Armenia Dram ', 'Vnom' => '1000', 'VcommonCode' => 'R01060 ', 'VnumCode' => '51', 'VcharCode' => 'AMD'],
            ['Vcode' => 'R01090B ', 'Vname' => 'Белорусский рубль ', 'VEngname' => 'Belarussian Ruble ', 'Vnom' => '1', 'VcommonCode' => 'R01090 ', 'VnumCode' => '933', 'VcharCode' => 'BYN'],
            ['Vcode' => 'R01095 ', 'Vname' => 'Бельгийский франк ', 'VEngname' => 'Belgium Franc ', 'Vnom' => '1000', 'VcommonCode' => 'R01095 ', 'VnumCode' => '56', 'VcharCode' => 'BEF'],
            ['Vcode' => 'R01100 ', 'Vname' => 'Болгарский лев ', 'VEngname' => 'Bulgarian lev ', 'Vnom' => '1', 'VcommonCode' => 'R01100 ', 'VnumCode' => '975', 'VcharCode' => 'BGN'],
            ['Vcode' => 'R01115 ', 'Vname' => 'Бразильский реал ', 'VEngname' => 'Brazil Real ', 'Vnom' => '1', 'VcommonCode' => 'R01115 ', 'VnumCode' => '986', 'VcharCode' => 'BRL'],
            ['Vcode' => 'R01135 ', 'Vname' => 'Венгерский форинт ', 'VEngname' => 'Hungarian Forint ', 'Vnom' => '100', 'VcommonCode' => 'R01135 ', 'VnumCode' => '348', 'VcharCode' => 'HUF'],
            ['Vcode' => 'R01200 ', 'Vname' => 'Гонконгский доллар ', 'VEngname' => 'Hong Kong Dollar ', 'Vnom' => '10', 'VcommonCode' => 'R01200 ', 'VnumCode' => '344', 'VcharCode' => 'HKD'],
            ['Vcode' => 'R01205 ', 'Vname' => 'Греческая драхма ', 'VEngname' => 'Greek Drachma ', 'Vnom' => '10000', 'VcommonCode' => 'R01205 ', 'VnumCode' => '300', 'VcharCode' => 'GRD'],
            ['Vcode' => 'R01215 ', 'Vname' => 'Датская крона ', 'VEngname' => 'Danish Krone ', 'Vnom' => '10', 'VcommonCode' => 'R01215 ', 'VnumCode' => '208', 'VcharCode' => 'DKK'],
            ['Vcode' => 'R01235 ', 'Vname' => 'Доллар США ', 'VEngname' => 'US Dollar ', 'Vnom' => '1', 'VcommonCode' => 'R01235 ', 'VnumCode' => '840', 'VcharCode' => 'USD'],
            ['Vcode' => 'R01239 ', 'Vname' => 'Евро ', 'VEngname' => 'Euro ', 'Vnom' => '1', 'VcommonCode' => 'R01239 ', 'VnumCode' => '978', 'VcharCode' => 'EUR'],
            ['Vcode' => 'R01270 ', 'Vname' => 'Индийская рупия ', 'VEngname' => 'Indian Rupee ', 'Vnom' => '100', 'VcommonCode' => 'R01270 ', 'VnumCode' => '356', 'VcharCode' => 'INR'],
            ['Vcode' => 'R01305 ', 'Vname' => 'Ирландский фунт ', 'VEngname' => 'Irish Pound ', 'Vnom' => '100', 'VcommonCode' => 'R01305 ', 'VnumCode' => '372', 'VcharCode' => 'IEP'],
            ['Vcode' => 'R01310 ', 'Vname' => 'Исландская крона ', 'VEngname' => 'Iceland Krona ', 'Vnom' => '10000', 'VcommonCode' => 'R01310 ', 'VnumCode' => '352', 'VcharCode' => 'ISK'],
            ['Vcode' => 'R01315 ', 'Vname' => 'Испанская песета ', 'VEngname' => 'Spanish Peseta ', 'Vnom' => '10000', 'VcommonCode' => 'R01315 ', 'VnumCode' => '724', 'VcharCode' => 'ESP'],
            ['Vcode' => 'R01325 ', 'Vname' => 'Итальянская лира ', 'VEngname' => 'Italian Lira ', 'Vnom' => '100000', 'VcommonCode' => 'R01325 ', 'VnumCode' => '380', 'VcharCode' => 'ITL'],
            ['Vcode' => 'R01335 ', 'Vname' => 'Казахстанский тенге ', 'VEngname' => 'Kazakhstan Tenge ', 'Vnom' => '100', 'VcommonCode' => 'R01335 ', 'VnumCode' => '398', 'VcharCode' => 'KZT'],
            ['Vcode' => 'R01350 ', 'Vname' => 'Канадский доллар ', 'VEngname' => 'Canadian Dollar ', 'Vnom' => '1', 'VcommonCode' => 'R01350 ', 'VnumCode' => '124', 'VcharCode' => 'CAD'],
            ['Vcode' => 'R01370 ', 'Vname' => 'Киргизский сом ', 'VEngname' => 'Kyrgyzstan Som ', 'Vnom' => '100', 'VcommonCode' => 'R01370 ', 'VnumCode' => '417', 'VcharCode' => 'KGS'],
            ['Vcode' => 'R01375 ', 'Vname' => 'Китайский юань ', 'VEngname' => 'China Yuan ', 'Vnom' => '10', 'VcommonCode' => 'R01375 ', 'VnumCode' => '156', 'VcharCode' => 'CNY'],
            ['Vcode' => 'R01390 ', 'Vname' => 'Кувейтский динар ', 'VEngname' => 'Kuwaiti Dinar ', 'Vnom' => '10', 'VcommonCode' => 'R01390 ', 'VnumCode' => '414', 'VcharCode' => 'KWD'],
            ['Vcode' => 'R01405 ', 'Vname' => 'Латвийский лат ', 'VEngname' => 'Latvian Lat ', 'Vnom' => '1', 'VcommonCode' => 'R01405 ', 'VnumCode' => '428', 'VcharCode' => 'LVL'],
            ['Vcode' => 'R01420 ', 'Vname' => 'Ливанский фунт ', 'VEngname' => 'Lebanese Pound ', 'Vnom' => '100000', 'VcommonCode' => 'R01420 ', 'VnumCode' => '422', 'VcharCode' => 'LBP'],
            ['Vcode' => 'R01435 ', 'Vname' => 'Литовский лит ', 'VEngname' => 'Lithuanian Lita ', 'Vnom' => '1', 'VcommonCode' => 'R01435 ', 'VnumCode' => '440', 'VcharCode' => 'LTL'],
            ['Vcode' => 'R01436 ', 'Vname' => 'Литовский талон ', 'VEngname' => 'Lithuanian talon ', 'Vnom' => '1', 'VcommonCode' => 'R01435 '],
            ['Vcode' => 'R01500 ', 'Vname' => 'Молдавский лей ', 'VEngname' => 'Moldova Lei ', 'Vnom' => '10', 'VcommonCode' => 'R01500 ', 'VnumCode' => '498', 'VcharCode' => 'MDL'],
            ['Vcode' => 'R01510 ', 'Vname' => 'Немецкая марка ', 'VEngname' => 'Deutsche Mark ', 'Vnom' => '1', 'VcommonCode' => 'R01510 ', 'VnumCode' => '276', 'VcharCode' => 'DEM'],
            ['Vcode' => 'R01510A ', 'Vname' => 'Немецкая марка ', 'VEngname' => 'Deutsche Mark ', 'Vnom' => '100', 'VcommonCode' => 'R01510 ', 'VnumCode' => '280', 'VcharCode' => 'DEM'],
            ['Vcode' => 'R01523 ', 'Vname' => 'Нидерландский гульден ', 'VEngname' => 'Netherlands Gulden ', 'Vnom' => '100', 'VcommonCode' => 'R01523 ', 'VnumCode' => '528', 'VcharCode' => 'NLG'],
            ['Vcode' => 'R01535 ', 'Vname' => 'Норвежская крона ', 'VEngname' => 'Norwegian Krone ', 'Vnom' => '10', 'VcommonCode' => 'R01535 ', 'VnumCode' => '578', 'VcharCode' => 'NOK'],
            ['Vcode' => 'R01565 ', 'Vname' => 'Польский злотый ', 'VEngname' => 'Polish Zloty ', 'Vnom' => '1', 'VcommonCode' => 'R01565 ', 'VnumCode' => '985', 'VcharCode' => 'PLN'],
            ['Vcode' => 'R01570 ', 'Vname' => 'Португальский эскудо ', 'VEngname' => 'Portuguese Escudo ', 'Vnom' => '10000', 'VcommonCode' => 'R01570 ', 'VnumCode' => '620', 'VcharCode' => 'PTE'],
            ['Vcode' => 'R01585 ', 'Vname' => 'Румынский лей ', 'VEngname' => 'Romanian Leu ', 'Vnom' => '10000', 'VcommonCode' => 'R01585 ', 'VnumCode' => '642', 'VcharCode' => 'ROL'],
            ['Vcode' => 'R01585F ', 'Vname' => 'Румынский лей ', 'VEngname' => 'Romanian Leu ', 'Vnom' => '10', 'VcommonCode' => 'R01585 ', 'VnumCode' => '946', 'VcharCode' => 'RON'],
            ['Vcode' => 'R01589 ', 'Vname' => 'СДР (специальные права заимствования) ', 'VEngname' => 'SDR ', 'Vnom' => '1', 'VcommonCode' => 'R01589 ', 'VnumCode' => '960', 'VcharCode' => 'XDR'],
            ['Vcode' => 'R01625 ', 'Vname' => 'Сингапурский доллар ', 'VEngname' => 'Singapore Dollar ', 'Vnom' => '1', 'VcommonCode' => 'R01625 ', 'VnumCode' => '702', 'VcharCode' => 'SGD'],
            ['Vcode' => 'R01665A ', 'Vname' => 'Суринамский доллар ', 'VEngname' => 'Surinam Dollar ', 'Vnom' => '1', 'VcommonCode' => 'R01665 ', 'VnumCode' => '968', 'VcharCode' => 'SRD'],
            ['Vcode' => 'R01670 ', 'Vname' => 'Таджикский сомони ', 'VEngname' => 'Tajikistan Ruble ', 'Vnom' => '10', 'VcommonCode' => 'R01670 ', 'VnumCode' => '972', 'VcharCode' => 'TJS'],
            ['Vcode' => 'R01670B ', 'Vname' => 'Таджикский рубл ', 'VEngname' => 'Tajikistan Ruble ', 'Vnom' => '10', 'VcommonCode' => 'R01670 ', 'VnumCode' => '762', 'VcharCode' => 'TJR'],
            ['Vcode' => 'R01700J ', 'Vname' => 'Турецкая лира ', 'VEngname' => 'Turkish Lira ', 'Vnom' => '1', 'VcommonCode' => 'R01700 ', 'VnumCode' => '949', 'VcharCode' => 'TRY'],
            ['Vcode' => 'R01710 ', 'Vname' => 'Туркменский манат ', 'VEngname' => 'Turkmenistan Manat ', 'Vnom' => '10000', 'VcommonCode' => 'R01710 ', 'VnumCode' => '795', 'VcharCode' => 'TMM'],
            ['Vcode' => 'R01710A ', 'Vname' => 'Новый туркменский манат ', 'VEngname' => 'New Turkmenistan Manat ', 'Vnom' => '1', 'VcommonCode' => 'R01710 ', 'VnumCode' => '934', 'VcharCode' => 'TMT'],
            ['Vcode' => 'R01717 ', 'Vname' => 'Узбекский сум ', 'VEngname' => 'Uzbekistan Sum ', 'Vnom' => '1000', 'VcommonCode' => 'R01717 ', 'VnumCode' => '860', 'VcharCode' => 'UZS'],
            ['Vcode' => 'R01720 ', 'Vname' => 'Украинская гривна ', 'VEngname' => 'Ukrainian Hryvnia ', 'Vnom' => '10', 'VcommonCode' => 'R01720 ', 'VnumCode' => '980', 'VcharCode' => 'UAH'],
            ['Vcode' => 'R01720A ', 'Vname' => 'Украинский карбованец ', 'VEngname' => 'Ukrainian Hryvnia ', 'Vnom' => '1', 'VcommonCode' => 'R01720 '],
            ['Vcode' => 'R01740 ', 'Vname' => 'Финляндская марка ', 'VEngname' => 'Finnish Marka ', 'Vnom' => '100', 'VcommonCode' => 'R01740 ', 'VnumCode' => '246', 'VcharCode' => 'FIM'],
            ['Vcode' => 'R01750 ', 'Vname' => 'Французский франк ', 'VEngname' => 'French Franc ', 'Vnom' => '1000', 'VcommonCode' => 'R01750 ', 'VnumCode' => '250', 'VcharCode' => 'FRF'],
            ['Vcode' => 'R01760 ', 'Vname' => 'Чешская крона ', 'VEngname' => 'Czech Koruna ', 'Vnom' => '10', 'VcommonCode' => 'R01760 ', 'VnumCode' => '203', 'VcharCode' => 'CZK'],
            ['Vcode' => 'R01770 ', 'Vname' => 'Шведская крона ', 'VEngname' => 'Swedish Krona ', 'Vnom' => '10', 'VcommonCode' => 'R01770 ', 'VnumCode' => '752', 'VcharCode' => 'SEK'],
            ['Vcode' => 'R01775 ', 'Vname' => 'Швейцарский франк ', 'VEngname' => 'Swiss Franc ', 'Vnom' => '1', 'VcommonCode' => 'R01775 ', 'VnumCode' => '756', 'VcharCode' => 'CHF'],
            ['Vcode' => 'R01790 ', 'Vname' => 'ЭКЮ ', 'VEngname' => 'ECU ', 'Vnom' => '1', 'VcommonCode' => 'R01790 ', 'VnumCode' => '954', 'VcharCode' => 'XEU'],
            ['Vcode' => 'R01795 ', 'Vname' => 'Эстонская крона ', 'VEngname' => 'Estonian Kroon ', 'Vnom' => '10', 'VcommonCode' => 'R01795 ', 'VnumCode' => '233', 'VcharCode' => 'EEK'],
            ['Vcode' => 'R01805 ', 'Vname' => 'Югославский новый динар ', 'VEngname' => 'Yugoslavian Dinar ', 'Vnom' => '1', 'VcommonCode' => 'R01804 ', 'VnumCode' => '890', 'VcharCode' => 'YUN'],
            ['Vcode' => 'R01810 ', 'Vname' => 'Южноафриканский рэнд ', 'VEngname' => 'S.African Rand ', 'Vnom' => '10', 'VcommonCode' => 'R01810 ', 'VnumCode' => '710', 'VcharCode' => 'ZAR'],
            ['Vcode' => 'R01815 ', 'Vname' => 'Вон Республики Корея ', 'VEngname' => 'South Korean Won ', 'Vnom' => '1000', 'VcommonCode' => 'R01815 ', 'VnumCode' => '410', 'VcharCode' => 'KRW'],
            ['Vcode' => 'R01820 ', 'Vname' => 'Японская иена ', 'VEngname' => 'Japanese Yen ', 'Vnom' => '100', 'VcommonCode' => 'R01820 ', 'VnumCode' => '392', 'VcharCode' => 'JPY'],
        ];
    }
}