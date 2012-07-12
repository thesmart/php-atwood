<?php

namespace Atwood\lib\oop;

/**
 * Yes, I know globals are bad. But they are convenient for runtime invariants.
 * This class is the same as Singleton, but can not be completely cleared.
 */
abstract class GlobalSingleton extends Singleton {
}
