<?php namespace SmartUpload;

/**
 * Class FileCollection
 * @package SmartUpload\
 */
class FileCollection implements \Iterator
{

    /**
     * @var array
     */
    protected $items = array();

    /**
     * @param null $items
     */
    public function __construct($items = null)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item instanceof File) {
                    $this->addItem($item);
                }
            }

            $this->rewind();
        }
    }

    /**
     * @param File $item
     * @return $this
     */
    public function addItem(File $item)
    {
        if (!empty($item)) {
            $this->items[] = $item;
        }
        return $this;
    }

    /**
     * Перемотка в начало
     */
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * Текущее значение
     * @return mixed
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * Возвращает ключ элемента
     * @return mixed
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @return mixed|void
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = key($this->items);
        return ($key !== null && $key !== false);
    }


    /**
     * @return int
     */
    public function count()
    {
        return sizeof($this->items);
    }
}