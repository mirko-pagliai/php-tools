# 1.x branch
## 1.1 branch
### 1.1.11
* added `IS_WIN` constant. The `is_win()` method is now deprecated and will be
    removed in a later version;
* added `assertIsDeprecated()` assert method;
* `assertFileExtension()` and `assertFileMime()` assert methods can take string
    or an array or a `Traversable` of files;
* all `ReflectionTrait` methods are now protected. The `setProperty()` method
    now returns the old value or `null`;
* `TestCaseTrait` is now deprecated and will be removed in a later version. Use
    `TestTrait` instead. The `createSomeFiles()` method has been removed and now
    it is a global function only for tests;
* fixed bug for `assertFilePerms()`. Now it works correctly and take permission
    values as string or octal value;
* fixed bug for `is_url()` function with no-string values.

### 1.1.10
* added `first_key()`, `first_value_recursive()`, `last_key()` and
    `last_value_recursive()` global functions;
* added `key_exists_or_fail()` function.

### 1.1.9
* `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` functions now have `$message` as second argument and
    `$exception` as third argument.

### 1.1.8
* `create_tmp_file()` and `safe_create_tmp_file()` methods now accept a 
    directory as a second argument and a prefix as the third argument.

### 1.1.7
* added `is_true_or_fail()` and `deprecationWarning()` global functions;
* added `create_file()` and `create_tmp_file()` global functions and
    `safe_create_file()` and `safe_create_tmp_file()` safe functions;
* added `assertException()` assert method;
* added some exception classes;
* `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` functions now throw specific exception classes.

### 1.1.6
* added `ends_with()` and `starts_with()` global functions.

### 1.1.5
* fixed `clean_url()` function, added third parameter `$removeTrailingSlash`.

### 1.1.4
* added `ReflectionTrait::getProperties()` method.

### 1.1.3
* added `BodyParser` class.

### 1.1.2
* added `$removeWWW` optional parameter to `clean_url()` global function.

### 1.1.1
* added `first_value()` and `last_value()` global functions.

### 1.1.0
* added `assertContainsInstanceOf()` assertion method. Removed
    `assertInstanceOf()` (you can use the original method).

## 1.0 branch
### 1.0.10
* added `FileArray` class.

### 1.0.9
* added `assertFilePerms()` assertion method.

### 1.0.8
* fixed bug for `unlink_recursive()` method with symlinks under Windows;
* `unlink_recursive()` returns void.

### 1.0.7
* added `dir_tree()` and `is_writable_resursive()` global functions;
* added `unlink_recursive()` and `safe_unlink_recursive()` functions.

### 1.0.6
* added `rmdir_recursive()` and `safe_rmdir_recursive()` functions;
* added `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` "or fail" functions;
* added `assertIsArrayNotEmpty()` assertion method.

### 1.0.5
* added `safe_copy()` and `safe_unserialized()` safe aliases.

### 1.0.4
* added `safe_mkdir()`, `safe_rmdir()`, `safe_symlink()` and `safe_unlink()`
    safe aliases;
* added `is_external_url()` global function;
* added `assertIsInt()` assertion method.

### 1.0.3
* added `clean_url()` and `is_slash_term()` global functions.

### 1.0.2
* added `Tools\Apache` class with some methods;
* added `Tools\TestSuite\TestCaseTrait` with some assertion methods;
* added `get_class_short_name()` and `get_hostname_from_url()` global functions;
* fixed `rtr()` global function. It can also use the `ROOT` environment variable.

### 1.0.1
* added `get_child_methods()` global function.

### 1.0.0
* first release.
