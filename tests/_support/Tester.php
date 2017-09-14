<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class Tester extends \Codeception\Actor
{
    use _generated\TesterActions;

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
   public function disableAutoload(Closure $closure) {
       $currentLoaders = spl_autoload_functions();
       foreach($currentLoaders as $currentLoader) {
           spl_autoload_unregister($currentLoader);
       }

       $closure();

       foreach($currentLoaders as $currentLoader) {
           spl_autoload_register($currentLoaders);
       }

       return $this;
   }
}
