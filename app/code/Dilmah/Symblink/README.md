Sitewards_Fix13929
==================

Currently, there is a bug that disallows the use of symlinks in the core. This reverts that decision.

Security Implications
---------------------

This change was made with the recent release of 2.2.3; a security release. However, it's not clear what disabling
symlinks is supposed to accomplish. After discussions with colleagues as well as some in the broader Magento
community, we determine the risk is acceptable to continue to use symlinks.

Include at your own risk.

Architecture
------------

A simple replacement of the Magento path validator.

Deployment
----------

There are no special deployment instructions.

Customization
-------------

This module has an extremely limited scope. Accordingly, extending it is not recommended.

Installation instruction
------------------------

This is packaged as a composer extension. It is not published to Packagist, nor any other package repository.
Please consult documentation on adding a git repository to composer and install it via this mechanism.

Thanks
------

@JosephMaxwell who came up with the fix. This is packing of his work.

Other
-----

- contact: mail@sitewards.com
- web:     http://www.sitewards.com
