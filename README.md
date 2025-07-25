# 自習室預約系統
<!-- 底下標籤來源參考寫法可至：https://github.com/Envoy-VC/awesome-badges#github-stats -->

![](https://img.shields.io/github/stars/hsiangfeng/README-Example-Template.svg)｜![](https://img.shields.io/github/forks/hsiangfeng/README-Example-Template.svg)｜![](https://img.shields.io/github/issues-pr/hsiangfeng/README-Example-Template.svg)｜![](https://img.shields.io/github/issues/hsiangfeng/README-Example-Template.svg)




## 功能

1. 登入系統：須先登入才能借用。
- 註冊
- 使用者登入/登出
- 會自動判別管理員或使用者
3. 使用者功能：
- 可查詢：自習室的座位資訊
 (如位置、插座有無、是否已被借出等)。
- 可查詢及取消：自己的預約紀錄。
- 可依不同日期、不同時段，預約 30 天內的座位。
- 若要預約座位，系統會檢核，包含：
⚫ 每人每時段，只能借用一個座位。
⚫ 每座位每時段，只能借給一個人。
⚫ 該時段不在「不開放借用」的範圍裡。
- 預約或取消成功後，會寄發 Email 通知借用者。

3. 管理員
- 可查詢及維護：自習室的座位資訊。
- 可查詢：所有人的預約紀錄。
- 設定「不開放借用」的日期。



## 畫面

### 註冊與登入畫面

![image](https://hackmd.io/_uploads/ryyC_qsIge.png)

![image](https://hackmd.io/_uploads/Bkkpd5iLge.png)


### 使用者介面
---
#### 使用者初始介面


![image](https://hackmd.io/_uploads/rJ6i_cj8ex.png)
#### 預約座位畫面
- 上面可以選擇自習室名稱，日期和時段
- X 為無法預約的教室，左下角有綠色代表有電源的座位

![image](https://hackmd.io/_uploads/SyL5uciUee.png)
#### 預約管理紀錄
![image](https://hackmd.io/_uploads/SkxIYqiIle.png)

### 管理員畫面
---
#### 管理員首頁
![image](https://hackmd.io/_uploads/S1XuGijLex.png)
#### 管理座位查詢
- 紅色代表被預約

![image](https://hackmd.io/_uploads/SkwtfjiIxl.png)

#### 查看所有預約紀錄
![image](https://hackmd.io/_uploads/S179zjiLgl.png)
#### 設定自習室不開方時段
- 可以設置時間段與封鎖理由

![image](https://hackmd.io/_uploads/BJqofosIxl.png)

#### 新增自習室
![image](https://hackmd.io/_uploads/ryZ6fosIee.png)
#### 管理自習室
![image](https://hackmd.io/_uploads/S1TpGiiUlg.png)




## 安裝

使用軟體為XAMPP
https://www.apachefriends.org/
需要安裝其中的Apache與MySQL

### 取得專案

```bash
git clone https://github.com/xCallmeEDtw/reserve_system.git reserve_system
```

### 將檔案移動到XAMPP的htdoc

```bash
cd reserve_system
mv * C:\xampp\htdocs
```


### 運行專案

運行XAMPP2的Apache與MySQL 
![image](https://hackmd.io/_uploads/HJHkMisUxx.png)



## 資料夾/檔案說明

- api
- PHPmailer
-



## 聯絡作者
- [mail] edward3a18@gmail.com