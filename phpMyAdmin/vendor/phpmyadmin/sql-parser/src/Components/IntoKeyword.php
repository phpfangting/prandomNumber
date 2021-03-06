<?php

/**
 * `INTO` keyword parser.
 */

namespace PhpMyAdmin\SqlParser\Components;

use PhpMyAdmin\SqlParser\Component;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Token;
use PhpMyAdmin\SqlParser\TokensList;

/**
 * `INTO` keyword parser.
 *
 * @category   Keywords
 *
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 */
class IntoKeyword extends Component
{
    /**
     * FIELDS/COLUMNS Options for `SELECT...INTO` statements.
     *
     * @var array
     */
    public static $FIELDS_OPTIONS = array(
        'TERMINATED BY' => array(1, 'expr'),
        'OPTIONALLY' => 2,
        'ENCLOSED BY' => array(3, 'expr'),
        'ESCAPED BY' => array(4, 'expr'),
    );

    /**
     * LINES Options for `SELECT...INTO` statements.
     *
     * @var array
     */
    public static $LINES_OPTIONS = array(
        'STARTING BY' => array(1, 'expr'),
        'TERMINATED BY' => array(2, 'expr'),
    );

    /**
     * Type of target (OUTFILE or SYMBOL).
     *
     * @var string
     */
    public $type;

    /**
     * The destination, which can be a table or a file.
     *
     * @var string|Expression
     */
    public $dest;

    /**
     * The name of the columns.
     *
     * @var array
     */
    public $columns;

    /**
     * The values to be selected into (SELECT .. INTO @var1).
     *
<<<<<<< HEAD
     * @var Expression[]
=======
     * @var ExpressionArray
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65
     */
    public $values;

    /**
     * Options for FIELDS/COLUMNS keyword.
     *
     * @var OptionsArray
     *
     * @see static::$FIELDS_OPTIONS
     */
    public $fields_options;

    /**
     * Whether to use `FIELDS` or `COLUMNS` while building.
     *
     * @var bool
     */
    public $fields_keyword;

    /**
     * Options for OPTIONS keyword.
     *
     * @var OptionsArray
     *
     * @see static::$LINES_OPTIONS
     */
    public $lines_options;

    /**
<<<<<<< HEAD
     * Constructor.
     *
     * @param string $type           Type of destination (may be OUTFILE).
     * @param string|Expression $dest           Actual destination.
     * @param array $columns        Column list of destination.
     * @param array $values         Selected fields.
     * @param OptionsArray $fields_options Options for FIELDS/COLUMNS keyword.
     * @param OptionsArray $fields_keyword Options for OPTINOS keyword.
     */
    public function __construct(
        $type = null,
        $dest = null,
        $columns = null,
        $values = null,
        $fields_options = null,
        $fields_keyword = null
    ) {
        $this->type = $type;
        $this->dest = $dest;
        $this->columns = $columns;
        $this->values = $values;
        $this->fields_options = $fields_options;
        $this->fields_keyword = $fields_keyword;
    }

    /**
=======
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65
     * @param Parser     $parser  the parser that serves as context
     * @param TokensList $list    the list of tokens that are being parsed
     * @param array      $options parameters for parsing
     *
     * @return IntoKeyword
     */
    public static function parse(Parser $parser, TokensList $list, array $options = array())
    {
        $ret = new self();

        /**
         * The state of the parser.
         *
         * Below are the states of the parser.
         *
         *      0 -----------------------[ name ]----------------------> 1
         *      0 ---------------------[ OUTFILE ]---------------------> 2
         *
         *      1 ------------------------[ ( ]------------------------> (END)
         *
         *      2 ---------------------[ filename ]--------------------> 1
         *
         * @var int
         */
        $state = 0;

        for (; $list->idx < $list->count; ++$list->idx) {
            /**
             * Token parsed at this moment.
             *
             * @var Token
             */
            $token = $list->tokens[$list->idx];

            // End of statement.
            if ($token->type === Token::TYPE_DELIMITER) {
                break;
            }

            // Skipping whitespaces and comments.
            if (($token->type === Token::TYPE_WHITESPACE) || ($token->type === Token::TYPE_COMMENT)) {
                continue;
            }

            if (($token->type === Token::TYPE_KEYWORD) && ($token->flags & Token::FLAG_KEYWORD_RESERVED)) {
<<<<<<< HEAD
                if (($state === 0) && ($token->keyword === 'OUTFILE')) {
=======
                if (($state === 0) && ($token->value === 'OUTFILE')) {
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65
                    $ret->type = 'OUTFILE';
                    $state = 2;
                    continue;
                }

                // No other keyword is expected except for $state = 4, which expects `LINES`
                if ($state !== 4) {
                    break;
                }
            }

            if ($state === 0) {
                if ((isset($options['fromInsert'])
                    && $options['fromInsert'])
                    || (isset($options['fromReplace'])
                    && $options['fromReplace'])
                ) {
                    $ret->dest = Expression::parse(
                        $parser,
                        $list,
                        array(
                            'parseField' => 'table',
                            'breakOnAlias' => true,
                        )
                    );
                } else {
                    $ret->values = ExpressionArray::parse($parser, $list);
                }
                $state = 1;
            } elseif ($state === 1) {
                if (($token->type === Token::TYPE_OPERATOR) && ($token->value === '(')) {
                    $ret->columns = ArrayObj::parse($parser, $list)->values;
                    ++$list->idx;
                }
                break;
            } elseif ($state === 2) {
                $ret->dest = $token->value;

                $state = 3;
            } elseif ($state == 3) {
                $ret->parseFileOptions($parser, $list, $token->value);
                $state = 4;
            } elseif ($state == 4) {
<<<<<<< HEAD
                if ($token->type === Token::TYPE_KEYWORD && $token->keyword !== 'LINES') {
=======
                if ($token->type === Token::TYPE_KEYWORD && $token->value !== 'LINES') {
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65
                    break;
                }

                $ret->parseFileOptions($parser, $list, $token->value);
                $state = 5;
            }
        }

        --$list->idx;

        return $ret;
    }

    public function parseFileOptions(Parser $parser, TokensList $list, $keyword = 'FIELDS')
    {
        ++$list->idx;

        if ($keyword === 'FIELDS' || $keyword === 'COLUMNS') {
            // parse field options
            $this->fields_options = OptionsArray::parse(
                $parser,
                $list,
                static::$FIELDS_OPTIONS
            );

            if ($keyword === 'FIELDS') {
                $this->fields_keyword = true;
            } else {
                $this->fields_keyword = false;
            }
        } else {
            // parse line options
            $this->lines_options = OptionsArray::parse(
                $parser,
                $list,
                static::$LINES_OPTIONS
            );
        }
    }

    /**
     * @param IntoKeyword $component the component to be built
     * @param array       $options   parameters for building
     *
     * @return string
     */
    public static function build($component, array $options = array())
    {
        if ($component->dest instanceof Expression) {
<<<<<<< HEAD
            $columns = !empty($component->columns) ? '(`' . implode('`, `', $component->columns) . '`)' : '';
=======
            $columns = !empty($component->columns) ?
                '(`' . implode('`, `', $component->columns) . '`)' : '';
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65

            return $component->dest . $columns;
        } elseif (isset($component->values)) {
            return ExpressionArray::build($component->values);
<<<<<<< HEAD
        }

        $ret = 'OUTFILE "' . $component->dest . '"';

        $fields_options_str = OptionsArray::build($component->fields_options);
        if (trim($fields_options_str) !== '') {
            $ret .= ($component->fields_keyword) ? ' FIELDS' : ' COLUMNS';
            $ret .= ' ' . $fields_options_str;
        }

        $lines_options_str = OptionsArray::build($component->lines_options, array('expr' => true));
        if (trim($lines_options_str) !== '') {
            $ret .= ' LINES ' . $lines_options_str;
        }

        return $ret;
=======
        } else {
            $ret = 'OUTFILE "' . $component->dest . '"';

            $fields_options_str = OptionsArray::build($component->fields_options);
            if (trim($fields_options_str) !== '') {
                $ret .= ($component->fields_keyword) ? ' FIELDS' : ' COLUMNS';
                $ret .= ' ' . $fields_options_str;
            }

            $lines_options_str = OptionsArray::build($component->lines_options, array('expr' => true));
            if (trim($lines_options_str) !== '') {
                $ret .= ' LINES ' . $lines_options_str;
            }

            return $ret;
        }
>>>>>>> 963d7f7adf76dfd7a7dbc54b828074e76cfb4d65
    }
}
