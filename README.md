BLOGS
   Partially hidden/paid articles.
   Paid for ads.


dashboard:
   best rating
   most viewed
   most commented today 
   new uncommon (new tags or original text - not found in Internet)
   categories
   

user
   user
      id
      email
      name
      password
      picture
      description
   auth

blog
   blog
      id
      user_id
      created_at
      title
      description
   category
      id
      title
      description
   article
      id
      category_id
      blog_id
      title
      content
      content_paid
      price
      created_at
      visits (+daily unique ips sum from tbl_visit)
      tags
      donate (bool, show donate link to account)      

   comment
      id
      user_id
      created_at
      content
      
   article_rating
      id
      user_id
      article_id
      value
   visit (daily cleaned)
      id
      ip
      article_id

payment
   account
      id
      user_id
      amount
   transaction «ЮКасса», Robokassa, PayMaster, Qiwi.
      id
      type (in/out)
      status
      amount
      created_at

ads (article author may select which ads to add to article)
   ...
   