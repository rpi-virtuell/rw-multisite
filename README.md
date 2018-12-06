# rw-multisite
shortcodes and more for multisite admins
Usable shortcodes: 
  * [rw_multisite_list_sites]     prints a list of all sites incl. description in a multisite
  * [rw_multisite_list_my_sites]  prints a list of the users sites 
  * [etool] Contentorganisation in Tabs und Accordions. Beispiel:
    ```
    [etool type="accordion" title_tag="h3" active="1"] 
    
      <h3>Erstes label</h3>
      
      Inhalte, die zwischen zwei h3 Überschreiften stehen werden 
      in einem Accordion mit dem Label der der h3 überschrift dargestellt  
      
      und noch mehr Inhalte…
      
      
      <h3>Die Library</h3>
      
      etool verwendet jquery ui
      
      Alternativ können die Inhalte auch in Tabs statt Accordion dargestellt werden 
            
      <h3>parameter</h3>
      
      title_tag = HTML-tag aus dessen Inhalt das  Label gebildet wird (default = h2)
      type = accordion | tabs (default = accordion)
      active = der active content (default="0")
    [/etool]
   ```
