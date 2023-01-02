# Magento 2 Product Variants **by ecomhouse**

## Description

**ecomhouse_productsvariants module is created to allow switching between product pages of simple products, by binding them together without using configurable product**

Thank to this module, you can create attributes group and after linking products to a given group of variants, the system displays an attribute swatcher on simple products, related with each other by attributes.
 

![methods][img1]
## Feature List

- boost store SEO by displaying product page with unique URL for every variant
- enable clients to easily switch between product pages of different variants within the view of the product page (there is no need to go back to the listing or use search)
- management from the CMS "Variants Group", where the attributes assigned to selected products are defined
- system displays an attribute swatcher on simple product pages (frontend), linked with each other by attributes


## How to install

#### **Add module manually**
1. Go to the app/code directory
2. Create new directories `EcomHouse/ProductVariants`
3. Unzip the module

**The entire path should look like this** `app/code/EcomHouse/ProductVariants`

4. Run commends

```
bin/magento setup:upgrade && bin/magento setup:static-content:deploy -f && bin/magento cache:clean
```

#### **Composer install**
1. Run commend

```
composer require ecomhouse/productvariants
```


### Other (config, dev tips)

##### **1. Change widget placement**
> After installation you can change widget placement in:
> `/view/frontend/layout/catalog_product_view.xml`
> by editing line
> `<move element="product.variants.wrapper" destination="product.info.main" after="product.info.price"/>`

## Help & docs
- [user guide PL](https://docs.google.com/presentation/d/10I9hlopEZfZa1vVYakOdzZ5Rkdr3E-_yeEC-hq8ZMok/edit?usp=sharing)
- [user guide EN](https://docs.google.com/presentation/d/1Y33sTS6Mz6a0E2j_8mZTQORoegpA9_DIgpB42lbsWU0/edit?usp=sharing)

[img1]: files/prev1.jpg
