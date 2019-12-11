# Magento 2 Google Client
This module is a wrapper module to be able to easily connect to all Google API services.

To add a new scope to the connection, add the following to the di.xml of your module:

```
<type name="Gigadesign\GoogleClient\Model\GoogleClientManager">
	<arguments>
		<argument name="scopes" xsi:type="array">
			<item name="calendar_read_only" xsi:type="string">\Google_Service_Calendar::CALENDAR_READONLY</item>
			<item name="calendar_events" xsi:type="string">\Google_Service_Calendar::CALENDAR_EVENTS</item>
		</argument>
	</arguments>
</type>
```

And make sure to add this module in the sequence of your own module.
