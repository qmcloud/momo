FROM golang:alpine AS builder

LABEL stage=gobuilder

ENV CGO_ENABLED 0
ENV GOOS linux
ENV GOPROXY https://goproxy.cn,direct

WORKDIR /build/zero

ADD go.mod .
ADD go.sum .
RUN go mod download
COPY . .
COPY service/user/cmd/rpc/systemuserget/etc /app/etc
RUN go build -ldflags="-s -w" -o /app/systemuserget service/user/cmd/rpc/systemuserget/systemuserget.go


FROM alpine

RUN apk update --no-cache && apk add --no-cache ca-certificates tzdata
ENV TZ Asia/Shanghai

WORKDIR /app
COPY --from=builder /app/systemuserget /app/systemuserget
COPY --from=builder /app/etc /app/etc

CMD ["./systemuserget", "-f", "etc/systemuserget.yaml"]
