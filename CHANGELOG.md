# 1.x branch
## 1.0 branch
### 1.0.7
* added `dir_tree()` and `is_writable_resursive()` global functions.

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
