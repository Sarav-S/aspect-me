<?php 
namespace Aspect;

class Advice
{
    const BEFORE = 'before';

    const AROUND = 'around';

    const AFTER = 'after';

    /**
     * Object Resolver Instance.
     *
     * @var callable
     */
    protected static $objectResolver;

    /**
     * The registered advice for particular join point.
     *
     * @var array
     */
    protected static $advices  = [];

    /**
     * Wrapper for before advice register.
     *
     * @param  string $id
     * @param  string $target
     * @param  string|callable  $macro
     * @param  int $sortOrder
     *
     * @return void
     */
    public static function before($id, $target, $macro, $sortOrder = 10)
    {
        static::register($id, static::BEFORE, $target, $macro, $sortOrder);
    }

    /**
     * Wrapper for around advice register.
     *
     * @param  string $id
     * @param  string $target
     * @param  string|callable  $macro
     * @param  int $sortOrder
     *
     * @return void
     */
    public static function around($id, $target, $macro, $sortOrder = 10)
    {
        static::register($id, static::AROUND, $target, $macro, $sortOrder);
    }


    /**
     * Wrapper for after advice register.
     *
     * @param  string $id
     * @param  string $target
     * @param  string|callable  $macro
     * @param  int $sortOrder
     *
     * @return void
     */
    public static function after($id, $target, $macro, $sortOrder = 10)
    {
        static::register($id, static::AFTER, $target, $macro, $sortOrder);
    }

    /**
     * Register a advice for particular join point.
     *
     * @param  string $id
     * @param  string $joinPoint
     * @param  string $target
     * @param  string|callable  $macro
     * @param  int $sortOrder
     *
     * @return void
     */
    public static function register($id, $joinPoint, $target, $macro, $sortOrder = 10)
    {
        list($target, $method) = static::parseTarget($target);

        if (!$method) {
            return;
        }

        static::$advices[$target][$method][$joinPoint][$id] = [
            'weaver' => $macro,
            'order' => $sortOrder,
        ];
    }

    /**
     * Get the registed advice for particular join point for a target.
     *
     * @param  string $joinPoint
     * @param  string $target
     *
     * @return array
     */
    public static function get($target)
    {
        list($target, $method) = static::parseTarget($target);

        if (!$method) {
            return isset(static::$advices[$target]) ?
                static::$advices[$target]
                : [];
        }

        return isset(static::$advices[$target][$method]) ?
                static::$advices[$target][$method]
                : [];
    }

    /**
     * Get all the registed advice.
     *
     * @return array
     */
    public static function all()
    {
        return static::$advices;
    }


    protected static function parseTarget($target)
    {
        return static::contains($target, '@') ? explode('@', $target, 2) : [$target, false];
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    protected static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the Object Resolver Instance.
     *
     * @return \Closure
     */
    public static function getObjectResolver()
    {
        return static::$objectResolver;
    }

    /**
     * Set the Object Resolver instance.
     *
     * @param  \Closure   $objectResolver
     * @return void
     */
    public static function setObjectResolver(\Closure $objectResolver)
    {
        static::$objectResolver = $objectResolver;
    }
}
