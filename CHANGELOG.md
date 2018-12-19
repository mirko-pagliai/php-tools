# 1.x branch
## 1.1 branch
### 1.1.7
* added `is_true_or_fail()` and `deprecationWarning()` global functions;
* added `create_file()` and `create_tmp_file()` global functions and
    `safe_create_file()` and `safe_create_tmp_file()` safe functions;
* added `assertException()` assert method;
* starting from this release, deprecated methods will generate a user-level error;
* added some exception classes;
* `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` functions are now deprecated and they will be
    removed from the `1.2.0` version. Use instead `is_true_or_fail()`.

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
