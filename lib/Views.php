<?php
namespace Lib;

class View
{
    private $data = [];
    private $sections = [];
    private $currentSection;
    private $layout;
    private $stacks = [];
    private $currentStack;
    private $controller = null;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function render($viewPath, $data = [])
    {
        $fullPath = $this->resolvePath($viewPath);

        if (!file_exists($fullPath)) {
            die("<pre>❌ View not found: {$fullPath}</pre>");
        }

        $this->data = array_merge($this->data, $data);
        extract($this->data);

        ob_start();
        include $fullPath;
        $content = ob_get_clean();

        if ($this->layout) {
            $layout = $this->layout;
            $this->layout = null;

            if (!isset($this->sections['content'])) {
                $this->sections['content'] = $content;
            }

            $layoutPath = $this->resolveLayoutPath($layout);
            if (!file_exists($layoutPath)) {
                die("<pre>❌ Layout not found: {$layoutPath}</pre>");
            }

            ob_start();
            extract($this->data);
            include $layoutPath;
            return ob_get_clean();
        }

        return $content;
    }

    /** Layouts */
    protected function extend($layout)
    {
        $this->layout = $layout;
    }

    /** Sections */
    protected function start($section)
    {
        $this->currentSection = $section;
        ob_start();
    }

    protected function end()
    {
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    protected function section($name)
    {
        return $this->sections[$name] ?? '';
    }

    /** Stacks (push / stack) */
    protected function push($stack)
    {
        $this->currentStack = $stack;
        ob_start();
    }

    protected function endPush()
    {
        $content = ob_get_clean();
        $this->stacks[$this->currentStack][] = $content;
        $this->currentStack = null;
    }

    protected function stack($name)
    {
        if (isset($this->stacks[$name])) {
            return implode("\n", $this->stacks[$name]);
        }
        return '';
    }

    // set ng-controller
    protected function controller($name){
        $this->controller = $name;
    }
    protected function getController(){
        return $this->controller;
    }

    /** Resolve view paths */
    private function resolvePath($viewPath)
    {
        if (file_exists($viewPath)) {
            return $viewPath;
        }

        $path = str_replace('.', DIRECTORY_SEPARATOR, $viewPath);
        $baseDir = rtrim(FRONTEND_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR;
        return $baseDir . $path . '.php';
    }

    private function resolveLayoutPath($layout)
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $layout);
        $baseDir = rtrim(FRONTEND_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR;

        $layoutPath = $baseDir . 'layouts' . DIRECTORY_SEPARATOR . $path . '.php';
        if (file_exists($layoutPath)) {
            return $layoutPath;
        }

        return $baseDir . $path . '.php';
    }
}
