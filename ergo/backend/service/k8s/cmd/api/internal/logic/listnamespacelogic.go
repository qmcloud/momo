package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type ListNameSpaceLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewListNameSpaceLogic(ctx context.Context, svcCtx *svc.ServiceContext) ListNameSpaceLogic {
	return ListNameSpaceLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *ListNameSpaceLogic) ListNameSpace(req types.RequestEmpty) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	nameSpaceList, _ := clientSet.CoreV1().Namespaces().List(context.TODO(), metav1.ListOptions{})
	items := make([]interface{}, 0, 1)
	for _, ns := range nameSpaceList.Items {
		item := make(map[string]interface{})
		item["name"] = ns.Name
		item["age"] = ns.CreationTimestamp.Time.Format("2006-01-02 15:04:05")
		item["status"] = ns.Status.Phase
		items = append(items, item)
	}
	return nil, errorx.NewCodeError(200, "查询成功", items)
}
