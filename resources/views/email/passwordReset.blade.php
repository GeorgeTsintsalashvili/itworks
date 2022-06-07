
<div style="width: 600px; margin-bottom: 30px; margin-top: 20px;">
 <img src="https://itworks.ge/images/general/logo.png" width="60%">
</div>

<p style="width: 600px; border: 1px solid #b4b4b4; padding: 10px; background-color: #f3f3f3; color: #3a3a3a;">
 <span style="font-size: 16px;">
  ეს შეტყობინება მოგივიდათ დავიწყებული პაროლის გადაყენების მოთხოვნის საფუძველზე. პაროლის აღდგენის ლინკის
  მოქმედების ვადა ამოიწურება <b>{{ $data['linkExpirationTime'] }}</b> წუთში. თუ თქვენ არ მოგითხოვიათ პაროლის გადაყენება,
  მაშინ შეგიძლიათ დააიგნოროთ შეტყობინება.
 </span>
</p>

<p style="margin-top: 30px;">
 <a style="text-decoration: none; padding: 10px; background-color: #4d4d4d; color: #fff; font-size: 16px;" href="{{ $data['url'] }}" target="_blank"> პაროლის გადაყენება </a>
</p>

<p style="margin-top: 30px; width: 600px;">
 <span style="font-size: 16px;">
  თუ პაროლის გადაყენების ლინკზე დაწკაპებისას ვერ გადადიხართ პაროლის აღდგენის გვერდზე,
  მაშინ შეგიძლიათ დააკოპიროთ ეს ბმული და ჩასვათ ბრაუზერში.
 </span>
</p>

<p style="margin-top: 30px; width: 600px;">
 <span style="font-size: 16px;">{{ $data['url'] }}</span>
</p>
