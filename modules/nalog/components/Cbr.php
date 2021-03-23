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

    /** @param $On_date 2021-03-24T00:00:00 | 2021-03-24 */
    public function GetCursOnDate($On_date){ return $this->call(static::methodName(), get_defined_vars()); }
    public function EnumValutes(bool $Seld){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateTime(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateTimeSeld(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDate(){ return $this->call(static::methodName(), get_defined_vars()); }
    public function GetLatestDateSeld(){ return $this->call(static::methodName(), get_defined_vars()); }
    /** @param $ValutaCode R01235 */
    public function GetCursDynamic($FromDate, $ToDate, $ValutaCode){ return $this->call(static::methodName(), get_defined_vars()); }
    public function KeyRate($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
    public function DragMetDynamic($FromDate, $ToDate){ return $this->call(static::methodName(), get_defined_vars()); }
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

}