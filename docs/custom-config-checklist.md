# Custom Configuration Export Checklist

## Custom Node Types
- `node.type.page.yml` - Basic page (with customizations)

## Custom Paragraphs  
- `paragraphs.paragraphs_type.ui_suite_uswds_accordion.yml`
- `paragraphs.paragraphs_type.ui_suite_uswds_accordion_item.yml`
- `paragraphs.paragraphs_type.ui_suite_uswds_card.yml`
- `paragraphs.paragraphs_type.ui_suite_uswds_card_group.yml`
- `paragraphs.paragraphs_type.ui_suite_uswds_card_single.yml`
- `paragraphs.paragraphs_type.test_paragraph_type.yml`

## Custom Vocabularies
- `taxonomy.vocabulary.research_areas.yml`
- `taxonomy.vocabulary.tags.yml`

## Custom Fields

### Media Fields (NRRL-specific)
- `field.storage.media.field_abbreviation.yml`
- `field.storage.media.field_description.yml`
- `field.storage.media.field_media_formula.yml`
- `field.storage.media.field_medium_num.yml`

### Paragraph Fields (UI Components)
**Accordion Fields:**
- `field.storage.paragraph.field_accordion_bordered.yml`
- `field.storage.paragraph.field_accordion_default_open.yml`
- `field.storage.paragraph.field_accordion_heading_level.yml`
- `field.storage.paragraph.field_accordion_item_body.yml`
- `field.storage.paragraph.field_accordion_item_title.yml`
- `field.storage.paragraph.field_accordion_multiselect.yml`
- `field.storage.paragraph.field_accordion_sections.yml`

**Card Fields:**
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
- `views.view.group_media.yml`
- `views.view.group_members.yml`
- `views.view.group_nodes.yml`
- `views.view.testing_ui_view.yml`

## Custom Group Configuration

### Group Types
- `group.type.nrrl.yml`
- `group.type.sheep_genetics.yml`

### Group Roles
**NRRL Roles:**
- `group.role.nrrl-admin.yml`
- `group.role.nrrl-admin_in.yml`
- `group.role.nrrl-admin_out.yml`
- `group.role.nrrl-anonymous.yml`
- `group.role.nrrl-member.yml`
- `group.role.nrrl-outsider.yml`

**Sheep Genetics Roles:**
- `group.role.sheep_genetics-admin.yml`
- `group.role.sheep_genetics-admin_in.yml`
- `group.role.sheep_genetics-admin_out.yml`
- `group.role.sheep_genetics-anonymous.yml`
- `group.role.sheep_genetics-member.yml`
- `group.role.sheep_genetics-outsider.yml`

### Group Relationship Types
- `group.relationship_type.nrrl-group_media-nrrl_catalog.yml`
- `group.relationship_type.nrrl-group_media-nrrl_document.yml`
- `group.relationship_type.nrrl-group_membership.yml`
- `group.relationship_type.nrrl-group_node-page.yml`
- `group.relationship_type.sheep_genetics-group_membership.yml`

## Custom Media Types
- `media.type.nrrl_catalog.yml`
- `media.type.nrrl_document.yml`

## Custom User Roles
- `user.role.administrator.yml`
- `user.role.content_administrator.yml`
- `user.role.content_editor.yml`

## Custom Menus
- `system.menu.nrrl.yml`
- `system.menu.secondary-menu.yml`

## Key Dependencies (Contrib Modules Required)

### Group Management
- `group` - Core group functionality
- `groupmedia` - Group-specific media management
- `groupmedia_paragraphs` - Group media + paragraphs integration
- `group_content_menu` - Group-based menus
- `flexible_permissions` - Granular group permissions

### UI Framework
- `ui_suite_uswds_paragraphs` - USWDS paragraph types
- `ui_suite_uswds_paragraphs_accordion` - Accordion components
- `ui_suite_uswds_paragraphs_card` - Card components
- `ui_patterns` - Pattern library system
- `ui_patterns_blocks` - Pattern blocks integration
- `ui_patterns_field_formatters` - Pattern field formatters
- `ui_patterns_layouts` - Pattern layouts
- `ui_patterns_library` - Pattern library
- `ui_patterns_views` - Pattern views integration
- `ui_skins` - Component styling
- `ui_styles` - Additional styling options

### Content Management
- `paragraphs` - Paragraph entities
- `entity_reference_revisions` - Revisionable references
- `layout_builder` - Layout building
- `media_library` - Media management
- `pathauto` - URL aliases
- `field_group` - Field grouping

### Administrative
- `admin_toolbar` - Enhanced toolbar
- `admin_toolbar_search` - Toolbar search
- `admin_toolbar_tools` - Admin tools
- `dashboards` - Admin dashboards
- `better_exposed_filters` - Enhanced filters
- `easy_breadcrumb` - Breadcrumb navigation
- `sitewide_alert` - Site alerts

### Authentication
- `openid_connect` - OpenID Connect
- `openid_connect_windows_aad` - Azure AD
- `externalauth` - External auth
- `key` - Key management

## Custom Theme
- **ui_suite_arsapps** - Main custom theme based on UI Suite USWDS

## Export Priority Order

1. **Enable modules** (dependencies first)
2. **User roles** (permission structure)
3. **Group types and roles** (multi-tenancy foundation)
4. **Taxonomies** (content categorization)
5. **Media types** (NRRL-specific content)
6. **Paragraph types** (UI components)
7. **Field storage** (field definitions)
8. **Group relationships** (content associations)
9. **Field instances** (field configurations)
10. **Views** (content display)
11. **Blocks and menus** (site structure)
12. **Theme settings** (visual configuration)