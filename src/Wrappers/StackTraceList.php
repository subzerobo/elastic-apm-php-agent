<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-11
 * Time: 15:08
 */

namespace Subzerobo\ElasticApmPhpAgent\Wrappers;


use Protos\StackTrace;

class StackTraceList
{
    /**
     * @var StackTrace[]
     */
    private $stackTraceList;


    /**
     * SpanContextData constructor.
     *
     * @param array $init_data
     *
     * @throws \Exception
     */
    public function __construct(array $init_data = [])
    {
        if (!empty($init_data)) {
            foreach ($init_data as $stackTraceArray){
                $stackTrace = new StackTrace();
                if (!empty($stackTraceArray)) {
                    $stackTrace->mergeFromJsonString(json_encode($stackTraceArray));
                    $this->stackTraceList[] = $stackTrace;
                }
            }
        }
    }

    /**
     * Creates the StackTrace with Provided parameters
     *
     * @param string|null $abs_path
     * @param int|null    $colno
     * @param string|null $contextLine
     * @param string|null $filename
     * @param string|null $function
     * @param bool        $library_frame
     * @param int|null    $lineno
     * @param string|null $module
     * @param array|null  $post_context
     * @param array|null  $pre_context
     * @param array|null  $vars
     *
     * @return StackTraceList
     * @throws \Exception
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 12:20
     */
    public function addStackTraceFromData(
        string $abs_path = null,
        int $colno = null,
        string $contextLine = null,
        string $filename = null,
        string $function = null,
        bool $library_frame = false,
        int $lineno = null,
        string $module = null,
        array $post_context = null,
        array $pre_context = null,
        array $vars = null
    ): self
    {
        $dbArr = [
            'abs_path' => $abs_path,
            'colno' => $colno,
            'context_line' => $contextLine,
            'filename' => $filename,
            'function' => $function,
            'library_frame' => $library_frame,
            'lineno' => $lineno,
            'module' => $module,
            'post_context' => $post_context,
            'pre_context' => $pre_context,
            'vars' => $vars,
        ];
        $stackTrace = new StackTrace();
        $stackTrace->mergeFromJsonString(json_encode($dbArr));
        $this->stackTraceList[] = $stackTrace;
        return $this;
    }

    /**
     * @param StackTrace $stackTrace
     *
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 12:28
     */
    public function addStackTrace(StackTrace $stackTrace) {
        $this->stackTraceList[] = $stackTrace;
    }

    /**
     * Gets the StackTrace Protobuf Object Array
     *
     * @return StackTrace[]
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-04-13 12:26
     */
    public function stackTraceList()
    {
        return $this->stackTraceList;
    }


}