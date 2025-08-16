# Configuration Sync Analysis

## Overview

This document provides a comprehensive analysis of all configuration files in the `config/sync/` directory, categorizing them into custom configurations that need to be exported from the source site to the target site.

## Custom Group Types

### NRRL Culture Collection Group
- **Group Type**: `group.type.nrrl.yml`
  - Label: "NRRL Culture Collection"
  - Description: "The Northern Regional Research Laboratory (NRRL) maintains the ARS Culture Collection, which is the largest publicly available collection of microorganisms in the world."
  - Creator membership: true
  - Creator wizard: true
  - Creator roles: nrrl-admin

### Sheep Genetics Group  
- **Group Type**: `group.type.sheep_genetics.yml`
  - Label: "Sheep Genetics"
  - Description: "The mission of the USDA-ARS Range Sheep Production Efficiency Research Unit..."
  - Creator membership: true
  - Creator wizard: true
  - Creator roles: sheep_genetics-admin

## Custom Group Roles

### NRRL Group Roles
- `group.role.nrrl-admin.yml`
- `group.role.nrrl-admin_in.yml`
- `group.role.nrrl-admin_out.yml`
- `group.role.nrrl-anonymous.yml`
- `group.role.nrrl-member.yml`
- `group.role.nrrl-outsider.yml`

### Sheep Genetics Group Roles
- `group.role.sheep_genetics-admin.yml`
- `group.role.sheep_genetics-admin_in.yml`
- `group.role.sheep_genetics-admin_out.yml`
- `group.role.sheep_genetics-anonymous.yml`
- `group.role.sheep_genetics-member.yml`
- `group.role.sheep_genetics-outsider.yml`

## Custom Group Relationship Types

### NRRL Relationships
- `group.relationship_type.nrrl-group_media-nrrl_catalog.yml`
- `group.relationship_type.nrrl-group_media-nrrl_document.yml`
- `group.relationship_type.nrrl-group_membership.yml`
- `group.relationship_type.nrrl-group_node-page.yml`

### Sheep Genetics Relationships
- `group.relationship_type.sheep_genetics-group_membership.yml`

## Custom Node Types

### Basic Page (Enhanced)
- **Node Type**: `node.type.page.yml`
  - Standard Drupal basic page with customizations
  - New revision: true
  - Preview mode: 1
  - Display submitted: false

## Custom Media Types

### NRRL-Specific Media Types
- **NRRL Catalog**: `media.type.nrrl_catalog.yml`
  - Label: "Catalog (NRRL)"
  - Description: "Revisionable. Allowed formats: .xlsx or .csv."
  - Source: file
  - New revision: true

- **NRRL Document**: `media.type.nrrl_document.yml`
  - Label: "Document (NRRL)" 
  - Source: file
  - New revision: true

## Custom Taxonomy Vocabularies

### Research Areas
- **Vocabulary**: `taxonomy.vocabulary.research_areas.yml`
  - Name: "Research Areas"
  - VID: research_areas
  - Description: "Domains/topics assigned to a research group"
  - Weight: 0

### Tags
- **Vocabulary**: `taxonomy.vocabulary.tags.yml`
  - Standard tags vocabulary for general categorization

## Custom Paragraph Types (UI Suite USWDS Components)

### Accordion Components
- `paragraphs.paragraphs_type.ui_suite_uswds_accordion.yml`
  - Label: "Accordion"
  - Description: "Based off of USWDS Accordion"

- `paragraphs.paragraphs_type.ui_suite_uswds_accordion_item.yml`
  - Label: "Accordion Item"
  - Individual accordion item component

### Card Components
- `paragraphs.paragraphs_type.ui_suite_uswds_card.yml`
  - Label: "Card"
  - USWDS Card component

- `paragraphs.paragraphs_type.ui_suite_uswds_card_group.yml`
  - Label: "Card Group"
  - Container for multiple cards

- `paragraphs.paragraphs_type.ui_suite_uswds_card_single.yml`
  - Label: "Card Single"
  - Single card wrapper component

### Test Paragraph Type
- `paragraphs.paragraphs_type.test_paragraph_type.yml`
  - Development/testing paragraph type

## Custom Fields

### NRRL Document Fields
- `field.storage.media.field_abbreviation.yml`
- `field.storage.media.field_description.yml`
- `field.storage.media.field_media_formula.yml`
- `field.storage.media.field_medium_num.yml`

### Accordion Fields
- `field.storage.paragraph.field_accordion_bordered.yml`
- `field.storage.paragraph.field_accordion_default_open.yml`
- `field.storage.paragraph.field_accordion_heading_level.yml`
- `field.storage.paragraph.field_accordion_item_body.yml`
- `field.storage.paragraph.field_accordion_item_title.yml`
- `field.storage.paragraph.field_accordion_multiselect.yml`
- `field.storage.paragraph.field_accordion_sections.yml`

### Card Fields
- `field.storage.paragraph.field_card_body_exdent.yml`
- `field.storage.paragraph.field_card_breakpoint_desktop.yml`
- `field.storage.paragraph.field_card_breakpoint_tablet.yml`
- `field.storage.paragraph.field_card_content.yml`
- `field.storage.paragraph.field_card_footer.yml`
- `field.storage.paragraph.field_card_footer_exdent.yml`
- `field.storage.paragraph.field_card_footer_url.yml`
- `field.storage.paragraph.field_card_group_cards.yml`
- `field.storage.paragraph.field_card_header.yml`
- `field.storage.paragraph.field_card_header_extent.yml`
- `field.storage.paragraph.field_card_header_first.yml`
- `field.storage.paragraph.field_card_make_flag.yml`
- `field.storage.paragraph.field_card_media.yml`
- `field.storage.paragraph.field_card_media_exdent.yml`
- `field.storage.paragraph.field_card_media_inset.yml`
- `field.storage.paragraph.field_card_single_card.yml`
- `field.storage.paragraph.field_card_single_heading_level.yml`
- `field.storage.paragraph.field_field_card_group_heading_l.yml`

## Custom Views

### Group Management Views
- **Group Media**: `views.view.group_media.yml`
  - Purpose: Display media items associated with groups
  - Base table: media_field_data
  - Access: group_permission 'access group_media overview'
  - Path: group/%group/media

- **Group Members**: `views.view.group_members.yml`
  - Purpose: Display group membership information

- **Group Nodes**: `views.view.group_nodes.yml`
  - Purpose: Display content nodes associated with groups

### Administrative Views
- **Testing UI View**: `views.view.testing_ui_view.yml`
  - Development/testing view

## Custom Menus

### NRRL Menu
- `system.menu.nrrl.yml`
  - Custom menu for NRRL-specific navigation

### Secondary Menu
- `system.menu.secondary-menu.yml`
  - Additional navigation menu

## Custom Blocks

### UI Suite ARS Apps Theme Blocks
- `block.block.ui_suite_arsapps_branding.yml`
- `block.block.ui_suite_arsapps_breadcrumbs.yml`
- `block.block.ui_suite_arsapps_content.yml`
- `block.block.ui_suite_arsapps_footer.yml`
- `block.block.ui_suite_arsapps_help.yml`
- `block.block.ui_suite_arsapps_hero.yml`
- `block.block.ui_suite_arsapps_heroarsappstheme.yml`
- `block.block.ui_suite_arsapps_heroarsappstheme_2.yml`
- `block.block.ui_suite_arsapps_heroarsappstheme_3.yml`
- `block.block.ui_suite_arsapps_local_actions.yml`
- `block.block.ui_suite_arsapps_local_tasks.yml`
- `block.block.ui_suite_arsapps_messages.yml`
- `block.block.ui_suite_arsapps_page_title.yml`

## Custom User Roles

- `user.role.administrator.yml`
- `user.role.content_administrator.yml`
- `user.role.content_editor.yml`

## Module Dependencies

### Core Group Functionality
- `group` - Core group functionality for multi-tenancy
- `groupmedia` - Group-specific media management  
- `groupmedia_paragraphs` - Group media integration with paragraphs
- `group_content_menu` - Group-based menu systems
- `flexible_permissions` - Granular permission control per group

### UI/UX Framework
- `ui_suite_uswds_paragraphs` - USWDS paragraph types for content building
- `ui_suite_uswds_paragraphs_accordion` - Accordion paragraph components
- `ui_suite_uswds_paragraphs_card` - Card paragraph components
- `ui_patterns` - Pattern library system for reusable components
- `ui_patterns_*` (blocks, field_formatters, layouts, library, views)
- `ui_skins` - Component styling system
- `ui_styles` - Additional styling options
- `ui_icons` - Icon management system
- `ui_icons_*` (field, library, media, patterns, picker)

### Content Management
- `paragraphs` - Paragraph entity system
- `entity_reference_revisions` - Revisionable entity references
- `layout_builder` - Layout building capabilities
- `media_library` - Media management interface
- `pathauto` - Automated URL alias generation
- `field_group` - Field grouping functionality

### Administrative Tools
- `admin_toolbar` - Enhanced admin toolbar
- `admin_toolbar_search` - Search functionality in toolbar
- `admin_toolbar_tools` - Additional admin tools
- `dashboards` - Administrative dashboards
- `better_exposed_filters` - Enhanced views filtering
- `easy_breadcrumb` - Improved breadcrumb navigation
- `sitewide_alert` - Site-wide alert system

### Authentication & Security
- `openid_connect` - OpenID Connect authentication
- `openid_connect_windows_aad` - Azure AD integration
- `externalauth` - External authentication support
- `key` - Key management system

### Development Tools
- `devel_generate` - Development content generation
- `ui_examples` - UI component examples
- `ui_examples_defaults` - Default UI examples

## Custom Themes

### Primary Theme
- **ui_suite_arsapps**: Custom ARS Apps theme
  - Settings: `ui_suite_arsapps.settings.yml`
  - Based on UI Suite USWDS framework
  - Custom SASS/CSS implementation

### Base Themes
- **ui_suite_uswds**: Base USWDS theme
  - Settings: `ui_suite_uswds.settings.yml`
  - USWDS component integration

## Configuration Dependencies Analysis

### High Priority Configurations (Must Export First)
1. **Module Dependencies**: All custom modules must be installed first
2. **Group Types**: Foundation for multi-tenant architecture
3. **Group Roles**: Define permissions within groups
4. **Taxonomy Vocabularies**: Content categorization structure
5. **Media Types**: Custom media handling for NRRL content
6. **User Roles**: Site-wide permission structure

### Medium Priority Configurations (Export Second)
1. **Paragraph Types**: UI component definitions
2. **Field Storage**: Field definitions for all entity types
3. **Group Relationships**: Define how content relates to groups
4. **Node Types**: Content type definitions

### Lower Priority Configurations (Export Last)
1. **Field Instances**: Specific field configurations per bundle
2. **Views**: Content display configurations
3. **Blocks**: Block placement and configuration
4. **Form/View Displays**: Entity form and display configurations
5. **Theme Settings**: Visual appearance configurations

## Export Sequence Recommendation

To ensure proper configuration import without dependency errors:

1. **Phase 1 - Foundation**
   - Enable all required modules via `core.extension.yml`
   - Import user roles
   - Import taxonomy vocabularies
   - Import group types and roles

2. **Phase 2 - Structure**  
   - Import media types
   - Import paragraph types
   - Import field storage definitions
   - Import group relationship types

3. **Phase 3 - Configuration**
   - Import field instance configurations
   - Import form/view display configurations
   - Import node type configurations
   - Import group base field overrides

4. **Phase 4 - Content Display**
   - Import views
   - Import blocks
   - Import menus
   - Import theme settings

This phased approach ensures dependencies are satisfied at each step of the configuration import process.