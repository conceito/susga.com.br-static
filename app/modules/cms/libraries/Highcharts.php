<?php

class Highcharts
{

    protected $series = array();

    protected $tmp = array();

    protected $keys = array();

    protected $category = '';

    protected $categories = array();

    function __construct()
    {
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function make($data)
    {
        //
//        foreach ($data as $k => $v)
//        {
//            $this->series[] = $k;
//            $this->tmp[$k]  = $this->transform($v);
//
//        }

        $categories = $this->makeCategories($data);
        $this->setCategories($categories);

        $series     = $this->makeSeries($data, $categories);

        //dd($data);
//        dd($series);

        return array(
            'categories' => $this->parseCategories($categories),
            'series'     => $series
        );
    }

    /**
     * @param $data
     * @return array|bool
     */
    public function transform($data)
    {

        $arr = $this->turnValuesToKeys();

        //        dd($data);

        foreach ($arr as $k => $v)
        {
            foreach ($data as $d)
            {
                if (isset($d[$k]))
                {
                    $arr[$k][] = $this->parseValue($d[$k]);
                }
            }

        }

        return $arr;
    }

    /**
     * @param null $arrKeys
     * @return array|bool
     */
    public function turnValuesToKeys($arrKeys = null)
    {
        $keys = $this->getKeys();
        if (empty($keys) && $arrKeys === null)
        {
            return false;
        }

        $arr = array_flip(($arrKeys) ? $arrKeys : $keys);

        foreach ($arr as $k => $a)
        {
            $arr[$k] = array();
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     */
    public function setKeys($keys)
    {
        $this->keys = $keys;
    }

    /**
     *
     * @param $val
     * @return int|string
     */
    public function parseValue($val)
    {
        if (is_numeric($val))
        {
            $val = (int)$val;
        }
        else if (magic_valid_data($val))
        {
            $val = formaPadrao($val);
        }

        return $val;
    }

    public function makeCategories($data)
    {
        $cat     = array();
        $catName = $this->getCategory();
        foreach ($data as $dt)
        {
            foreach ($dt as $d)
            {
                if (isset($d[$catName]))
                {
                    $cat[] = $d[$catName];
                }
            }
        }

        $cat = array_unique($cat);

        sort($cat);

        $cat = array_merge(array(), $cat);

        return $cat;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function makeSeries($data, $categories)
    {
        //        dd($data);
        $c = $this->getCategory();

        $aSerie = array();

        foreach ($data as $k => $serie)
        {
            $aSerie[$k] = $this->equilizeWithCategories($serie, $categories);
//            dd($aSerie);

        }

        return $aSerie;
    }

    public function equilizeWithCategories($seriesData, $categories)
    {
//        dd($categories);
//        dd($seriesData);

        $array = array();
        foreach ($categories as $cat)
        {
            foreach ($seriesData as $s)
            {

                $hasData  = false;
                $hasValue = 0;

                if ($cat == $s['data'])
                {
                    $hasData  = true;
                    $hasValue = $s['por_dia'];
                    break;
                }
            }

            $array[] = $this->parseValue($hasValue);

        }

//        dd($array);

        return $array;
    }

    public function heighest($arrayData)
    {

        $heighest     = 0;
        $lastHeighest = array();
        // get highest
        foreach ($arrayData as $item)
        {
            $keys = $this->getKeys();
            $it   = $item[$keys[0]];
            if (count($it) > $heighest)
            {
                $heighest     = count($it);
                $lastHeighest = $item;
            }
        }

        return $lastHeighest;

    }

    /**
     * @param $categories
     * @return mixed
     */
    public function parseCategories($categories)
    {
        $cats = array();
        foreach($categories as $cat)
        {
            $cats[] = formaPadrao($cat);
        }
        return $cats;
    }
}