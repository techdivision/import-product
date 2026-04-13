# AGENTS.md - import-product

## Zweck & Verantwortung

Das `import-product` Modul bietet **Product Import-Funktionalität** für das Pacemaker Import-System. Es ist ein **Tier 4 Modul** und dient als Basis für alle Product-bezogenen Importer.

**Hauptverantwortung:**
- Simple Product Import und Verwaltung
- Product Attributes Import
- Product Inventory Management
- Repository Pattern für Product-Persistierung
- Service Layer für Product-Verarbeitung
- Observer Pattern für Product-Hooks
- Event-Driven für Bulk Operations
- 11 Dependents (bundle, grouped, link, media, msi, tier-price, url-rewrite, variant, converter-product-attr, converter-product-cat, product-ee)

## Architektur & Design Patterns

### Kern-Klassen
- **ProductRepository**: Persistierung von Produkten
- **ProductAttributeRepository**: Persistierung von Product Attributes
- **ProductProcessor**: Service Layer für Product-Verarbeitung
- **ProductObserver**: Observer für Product-Hooks
- **ProductAttributeObserver**: Observer für Product Attributes

### Verwendete Patterns
- **Observer Pattern**: Für Product-Hooks
- **Repository Pattern**: Für Daten-Persistierung
- **Service Layer**: Für Business Logic
- **Event-Driven**: Für Bulk Operations
- **Factory Pattern**: Für Object-Erstellung

## Abhängigkeiten

### Externe Pakete
- **Keine** - Nur Importer-Implementierungen

### TechDivision Dependencies
- **import** ^18.1 - Core Framework

### Abhängig von diesem Modul (11 Reverse Dependencies)
1. **import-product-bundle** - Bundle Product Importer
2. **import-product-grouped** - Grouped Product Importer (via link)
3. **import-product-link** - Product Link Importer
4. **import-product-media** - Product Media Importer
5. **import-product-msi** - MSI Stock Importer
6. **import-product-tier-price** - Tier Price Importer
7. **import-product-url-rewrite** - URL Rewrite Importer
8. **import-product-variant** - Configurable Product Importer
9. **import-converter-product-attribute** - Product Attribute Converter
10. **import-converter-product-category** - Product Category Converter
11. **import-product-ee** - EE Product Extensions

## Wichtige Entry Points

### Repository Klassen
```php
// Product Repository
ProductRepository::create($row): void
ProductRepository::update($row): void
ProductRepository::findBySku($sku): array

// Product Attribute Repository
ProductAttributeRepository::create($row): void
ProductAttributeRepository::findByCode($code): array
```

### Observer Klassen
```php
// Product Observer
ProductObserver::handle($row): void

// Product Attribute Observer
ProductAttributeObserver::handle($row): void
```

## Events & Extension Points

### Events
- **BeforeProductCreateEvent**: Vor Product-Create
- **AfterProductCreateEvent**: Nach Product-Create
- **BeforeProductUpdateEvent**: Vor Product-Update
- **AfterProductUpdateEvent**: Nach Product-Update

### Listeners
- **ProductUrlRewriteListener**: Für URL-Rewrite Management
- **ProductCacheListener**: Für Product-Caching
- **ProductInventoryListener**: Für Inventory Management

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 4 Modul**: Basis für alle Product-bezogenen Importer
2. **Product-fokussiert**: Spezialisiert auf Simple Product Import
3. **Observer Pattern**: Für Product-Hooks
4. **Repository Pattern**: Für Daten-Persistierung
5. **Event-Driven**: Für Bulk Operations
6. **11 Dependents**: Basis für spezialisierte Product Importer
7. **Zentral**: Wichtigstes Modul nach Core Framework

### Bei Änderungen
- **Product-Kompatibilität**: Beachte Product-Struktur
- **Observer-Kompatibilität**: Neue Observers sollten optional sein
- **Event-Kompatibilität**: Neue Events sollten optional sein
- **Backward Compatibility**: Alte Imports sollten noch funktionieren
- **11 Dependents**: Alle müssen kompatibel sein

### Implementierungs-Hinweise
- Nutze Observer Pattern für Custom Product-Processing
- Beachte Product-Validierung bei Imports
- Erwäge SKU-Validierung und Eindeutigkeit
- Beachte Inventory-Management

## Bekannte Einschränkungen

- **Simple Products Only**: Spezialisierte Product-Typen sind in anderen Modulen
- **Keine Product-Validierung**: Validierung erfolgt in Importern
- **Keine Bundle-Features**: Bundle-Features sind in `import-product-bundle`
- **Keine Configurable-Features**: Configurable-Features sind in `import-product-variant`
- **Keine EE-Features**: EE-Features sind in `import-product-ee`

## Zusammenfassung

`import-product` ist ein **Tier 4 Modul**, das Product Import-Funktionalität für das Pacemaker-System bietet. Es ist die Basis für alle Product-bezogenen Importer und das zentrale Modul für Product-Imports.

**Für Agenten:** Verstehe dieses Modul als **Product Importer** mit Observer Pattern, Repository Pattern, und Event-Driven Architektur. Es ist eines der wichtigsten Module im System.
