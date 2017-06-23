## 前言
所有的请求内容都是application/json，且所有请求都会附带Access-Token自定义头部用来让API服务器进行身份认证，未登录的用户Access-Token为空

## User
### 注册

#### HTTP Request
POST /users
#### 传入参数
| 参数名 | 必须 | 说明 |
| :-- | :-- | :-- |
| email | true |用户邮箱 |
| password | true | 用户密码 |
#### 返回值
| 参数名 | 类型 | 说明 |
| :-- | :-- | :-- |
| id | int | 用户id |
| access_token | string | 身份认证令牌 |
| username | string | 用户昵称 |
| gender | string | 性别(候选值not_specified,male,female) |
| realname | string | 真实姓名 |
| email | string | 用户邮箱 |
| auth | string | 用户身份(候选值user, admin) |

#### 例子
```Json
 {
  "id": "1",
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0OTcwMTA3NTAsImF1dGgiOiJ1bmRlcmdyYWR1YXRlIiwidXNlcl9pZCI6IjIifQ.FJOSGojP_gCIIveKDyARLDdPIJeSafPyc1UYtiVmjqk",
  "username": "lwio",
  "realname": "keke",
  "email": "443474713@qq.com",
  "auth": "admin",
}
```
------
### 登录

#### HTTP Request
POST /users/signin 对数据库进行身份验证，并将其重定向到新的会话或重定向到登录
#### 传入参数
|参数名|说明|
|:--|:--|
|user_email|用户邮箱|
|password|用户密码|
#### 返回值

|参数名|类型|说明|
|:--|:--|:--|
|token|string|加密信息|
|user_id|int|用户id|
|user_name|string|用户昵称|
|real_name|string|真实姓名|
|user_email|string|用户邮箱|
|auth|string|用户身份|

#### 例子
```
{
  "user_id": "2",
  "user_name": "",
  "real_name": "",
  "user_email": "1101010@qq.com0",
  "auth": "undergraduate",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0OTcwMTA3NTAsImF1dGgiOiJ1bmRlcmdyYWR1YXRlIiwidXNlcl9pZCI6IjIifQ.FJOSGojP_gCIIveKDyARLDdPIJeSafPyc1UYtiVmjqk"
}
```
    
### 获取用户信息
GET /users 获取所有注册用户信息 (需要提供分页和filed两个功能)

GET /users/:id 获取id用户信息
#### 传入参数
空

#### 返回值

| 参数名 | 类型 | 说明 |
| :-- | :-- | :-- |
| id | int | 用户id |
| username | string | 用户昵称 |
| realname | string | 真实姓名 |
| email | string | 用户邮箱 |
| auth | string | 用户身份 |

#### 例子
```

  {
    "id": 3,
    "username": "lwio",
    "realname": "keke",
    "email": "443474713@qq.com",
    "auth": "admin",
  },
```

## Music
### 获取音乐情况
**注意，所有演唱作曲作词都是音乐人，作为一个类型的对象。**

GET /music

GET /music/:id
#### 传入参数
|参数名|说明|
|:--|:--|
|-|-|

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|音乐id|
|name|string|歌名|
| cover_url | string | 封面图片路径 |
|singer|object|演唱者|
|composer|object|作曲者|
|lyricist|object|作词者|
| lyric_url | string | 歌词请求url |
| album | object | 所属专辑 |
| src_url | string | 音频资源路径 |
| published_at | string | UTC时间(2009-01-17T20:14:40Z) |
#### 例子
```
{
    id: '123',
    name: "miaomiao",
    coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
    singer: {
        id: '1',
        name: 'adyden'
    },
    composer: {
        id: '3',
        name: 'fuck'
    },
    lyricist: {
        id: '3',
        name: 'fuck',
    },
    lyric_url: "https://api.darlin.me/music/lyric/12/",
    album: {
        id: '5',
        name: 'album test',
        cover_url: "",
        songs_num: 12,
    },
    src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
    published_at: "2009-01-17T20:14:40Z",
}
```

### 上传新音乐
**注意，这个需要验证管理员权限**

POST /music
#### 传入参数
|参数名| 类型 | 必须 | 默认 | 说明|
|:--|:--|:--|:--|:--|
|name|string|true|-|歌名|
| cover_url | string | false | 默认图片url | 封面图片路径 |
|singer|object|true|-|演唱者|
|composer_id|int|false| null |作曲者|
|lyricist_id|int|false| null |作词者|
| lyric_url | string |false| null |歌词请求url |
| album | object | false | null |所属专辑 |
| src_url | string | true | - | 音频资源路径 |
| published_at | string | true | - |  UTC时间(2009-01-17T20:14:40Z) |

#### 返回值
|参数名|类型|说明|
|:--|:--|:--|
|id|int|音乐id|
|name|string|歌名|
| cover_url | string | 封面图片路径 |
|singer|object|演唱者|
|composer|object|作曲者|
|lyricist|object|作词者|
| lyric_url | string | 歌词请求url |
| album | object | 所属专辑 |
| src_url | string | 音频资源路径 |
| published_at | string | UTC时间(2009-01-17T20:14:40Z) |
#### 例子
```
{
    id: '123',
    name: "miaomiao",
    coverr_url: "http://img4.duitang.com/uploads/item/201404/15/20140415093826_SzcNe.thumb.700_0.jpeg",
    singer: {
        id: '1',
        name: 'adyden'
    },
    composer: {
        id: '3',
        name: 'fuck'
    },
    lyricist: {
        id: '3',
        name: 'fuck',
    },
    lyric_url: "https://api.darlin.me/music/lyric/12/",
    album: {
        id: '5',
        name: 'album test',
        cover_url: "",
        songs_num: 12,
    },
    src: "http://data.5sing.kgimg.com/G104/M09/1C/1D/qA0DAFk1fVGAGWkMAOMuQpygo8g155.mp3",
    published_at: "2009-01-17T20:14:40Z",
}
```

## 待补充
PUT /music/:id      修改歌曲信息(管理员权限) 

DELETE /music/:id   删除歌曲信息(管理员权限)

---

GET /lyrics        获取歌词列表(需要提供分页和filed以及是否审核三个功能)

GET /lyrics/:id    获取歌词对象信息(数据格式找我要)

POST /lyrics       上传歌词(数据格式找我要)

PUT /lyrics/:id    修改歌词对象

DELETE /lyrics/:id 删除歌词(管理员权限)

POST /lyrics/:id/approve  审核歌词(管理员权限)

---

GET /users/:id/collections  获取某用户的收藏(需要提供分页)

POST /music/:id/collect     用户添加歌曲到收藏

POST /music/:id/review      用户评论歌曲
---
