print("WELCOME SELECT OPTION BELOW")
print(" Select currency Below")
currencySelection = input("Select currency: 1. USD 2. ZiG")
if currencySelection == 1:
    print("Want to purchase using USD")
    print("1. whatsapp bundle 2. Facebook Bundle 3. General data 4. Private ")
    bundleSelection = input("Select Option")
    if bundleSelection == 1:
        print(" USD Whatsapp bundles")
    elif bundleSelection == 2:
        print(" USD Facebook bundles")
    elif bundleSelection == 3:
        print(" USD General bundles")
    elif bundleSelection == 4:
        print(" USD Private wifi bundles")
    else:  # bundleSelection > 4 | bundleSelection <= 0:
        print("Invalid option try again!")
elif currencySelection == 2:
    print("Want to purchase using USD")
    print("1. whatsapp bundle 2. Facebook Bundle 3. General data 4. Private ")
    ZiGbundleSelection = input(int("Select Option"))
    if ZiGbundleSelection == 1:
        print(" ZiG Whatsapp bundles")
    elif ZiGbundleSelection == 2:
        print(" ZiG Facebook bundles")
    elif ZiGbundleSelection == 3:
        print(" ZiG General bundles")
    elif ZiGbundleSelection == 4:
        print(" ZiG Private wifi bundles")
    
    else:  # bundleSelection > 4 | bundleSelection <= 0:
        print("Invalid option try again!")
else:
    print("Invalid option try again! value should be 1 or 2")

