<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\models\b24;

use yii\base\BaseObject;
use yii\helpers\StringHelper;

/**
 * Класс ColumnSchema описывает метаданные столбца в таблице базы данных.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ColumnSchema extends BaseObject
{
    /**
     * @var string имя этого столбца (без кавычек).
     */
    public $name;
    /**
     * @var bool может ли этот столбец быть нулевым.
     */
    public $allowNull;
    /**
     * @var string абстрактный тип этого столбца. Возможные абстрактные типы включают:
     * char, string, text, boolean, smallint, integer, bigint, float, decimal, datetime,
     * временная метка, время, дата, двоичный файл и деньги.
     */
    public $type;
    /**
     * @var string тип PHP этого столбца. Возможные типы PHP включают:
     * «строка», «логическое», «целое», «двойное», «массив».
     */
    public $phpType;
    /**
     * @var string тип БД этого столбца. Возможные типы БД зависят от типа СУБД.
     */
    public $dbType;
    /**
     * @var mixed значение по умолчанию для этого столбца
     */
    public $defaultValue;
    /**
     * @var array перечисляемые значения. Это устанавливается, только если столбец объявлен как перечисляемый тип.
     */
    public $enumValues;
    /**
     * @var int отображать размер столбца.
     */
    public $size;
    /**
     * @var int точность данных столбца, если они числовые.
     */
    public $precision;
    /**
     * @var int масштаб данных столбца, если он числовой.
     */
    public $scale;
    /**
     * @var bool является ли этот столбец первичным ключом
     */
    public $isPrimaryKey;
    /**
     * @var bool является ли этот столбец автоинкрементным
     */
    public $autoIncrement = false;
    /**
     * @var bool является ли этот столбец беззнаковым. Это имеет смысл только
     * когда [[type]] имеет значение `smallint`, `integer` или `bigint`
     */
    public $unsigned;
    /**
     * @var string комментарий к этой колонке. Не все СУБД поддерживают это.
     */
    public $comment;


    /**
     * Преобразует входное значение в соответствии с [[phpType]] после извлечения из базы данных.
     * Если значение равно null или [[Expression]], оно не будет преобразовано.
     * @param mixed $value input value
     * @return mixed converted value
     */
    public function phpTypecast($value)
    {
        return $this->typecast($value);
    }

    /**
     * Преобразует входное значение в соответствии с [[type]] и [[dbType]] для использования в запросе к базе данных.
     * Если значение равно null или [[Expression]], оно не будет преобразовано.
     * @param mixed $value input value
     * @return mixed преобразованное значение. Это также может быть массив, содержащий значение в качестве первого элемента.
     * и тип PDO в качестве второго элемента.
     */
    public function dbTypecast($value)
    {
        // Реализация по умолчанию делает то же самое, что и кастинг для PHP, но это должно быть возможно.
        // чтобы переопределить это аннотацией явного типа PDO.
        return $this->typecast($value);
    }

    /**
     * Преобразует входное значение в соответствии с [[phpType]] после извлечения из базы данных.
     * Если значение равно null или [[Expression]], оно не будет преобразовано.
     * @param mixed $value input value
     * @return mixed converted value
     * @since 2.0.3
     */
    protected function typecast($value)
    {
        //TODO переписать для Б24
        \Yii::warning($value, 'typecast($value)');
        if ($value === ''
            && !in_array(
                $this->type,
                [
                    Schema::TYPE_TEXT,
                    Schema::TYPE_STRING,
                    Schema::TYPE_BINARY,
                    Schema::TYPE_CHAR
                ],
                true)
        ) {
            return null;
        }

        if ($value === null
            || gettype($value) === $this->phpType
            || $value instanceof ExpressionInterface
            || $value instanceof Query
        ) {
            return $value;
        }

        if (is_array($value)
            && count($value) === 2
            && isset($value[1])
            && in_array($value[1], $this->getPdoParamTypes(), true)
        ) {
            return new PdoValue($value[0], $value[1]);
        }

        switch ($this->phpType) {
            case 'resource':
            case 'string':
                if (is_resource($value)) {
                    return $value;
                }
                if (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    return StringHelper::floatToString($value);
                }
                if (is_numeric($value)
                    && ColumnSchemaBuilder::CATEGORY_NUMERIC === ColumnSchemaBuilder::$typeCategoryMap[$this->type]
                ) {
                    // https://github.com/yiisoft/yii2/issues/14663
                    return $value;
                }

                return (string) $value;
            case 'integer':
                return (int) $value;
            case 'boolean':
                // treating a 0 bit value as false too
                // https://github.com/yiisoft/yii2/issues/9006
                return (bool) $value && $value !== "\0";
            case 'double':
                return (float) $value;
        }

        return $value;
    }

    /**
     * @return int[] array of numbers that represent possible PDO parameter types
     */
    private function getPdoParamTypes()
    {
        return [\PDO::PARAM_BOOL, \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_LOB, \PDO::PARAM_NULL, \PDO::PARAM_STMT];
    }
}
