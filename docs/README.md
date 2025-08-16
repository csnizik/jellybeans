# Documentation Directory

This directory contains documentation for the ARS Apps Drupal site configuration analysis.

## Files

### config-sync-analysis.md
**Comprehensive Configuration Analysis**

Detailed analysis of all configuration files in the `config/sync/` directory, including:
- Full categorization by entity type (groups, media, paragraphs, fields, etc.)
- Descriptions of each custom configuration
- Dependency analysis and import sequencing
- Module requirements and theme configurations

### custom-config-checklist.md
**Export Checklist Summary**

Concise checklist of custom configurations that need to be exported from source to target site:
- Quick reference list by category
- Essential custom configurations only
- Module dependencies required
- Recommended export priority order

## Scripts

### ../scripts/export-custom-config.sh
**Automated Export Script**

Bash script to automate the export process from source site:
- Exports configurations in proper dependency order
- Includes all custom configurations identified
- Provides step-by-step execution with status messages
- Ready to run on source site with proper Drupal/Drush setup

## Usage

1. **For Analysis**: Review `config-sync-analysis.md` for complete understanding of all configurations
2. **For Quick Reference**: Use `custom-config-checklist.md` as a checklist during migration
3. **For Automation**: Run `../scripts/export-custom-config.sh` on the source site to export all configurations

## Configuration Migration Process

1. **Source Site**: Run export script to generate configuration YAML files
2. **Target Site**: Copy exported files to `config/sync/` directory
3. **Import**: Run `drush config:import` following the recommended sequence in the analysis document

## Key Custom Configurations Identified

- **2 Group Types**: NRRL Culture Collection, Sheep Genetics
- **12 Group Roles**: 6 for each group type
- **5 Group Relationship Types**: Media and membership relationships
- **2 Custom Media Types**: NRRL-specific catalog and document types
- **6 Paragraph Types**: USWDS component integration (accordion, cards, etc.)
- **26 Custom Fields**: For NRRL media and UI components
- **4 Custom Views**: Group management interfaces
- **Multiple Dependencies**: 30+ contrib modules required for full functionality

See individual files for complete details and implementation guidance.